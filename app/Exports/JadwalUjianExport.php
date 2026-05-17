<?php

namespace App\Exports;

use App\Models\JadwalUjian;
use App\Models\MasterDataProgramStudi;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\JadwalUjianPerProdiSheet;

class JadwalUjianExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function sheets(): array
    {
        $sheets = [];

        $query = JadwalUjian::with(['mataKuliah', 'kelas.programStudi', 'dosen', 'ruangan'])
            ->where('periode_id', $this->filters['periode_id'])
            ->where('tipe', $this->filters['tipe']);

        if (!empty($this->filters['status'])) {
            $query->where('status_data', $this->filters['status']);
        }

        $jadwal = $query->orderBy('tanggal')->orderBy('jam_mulai')->get();

        // Get all program studi that have jadwal
        $prodiIds = $jadwal->pluck('kelas.program_studi_id')->filter()->unique();
        $programStudis = MasterDataProgramStudi::whereIn('id', $prodiIds)->get()->keyBy('id');

        $groupedJadwal = $jadwal->groupBy(function($item) {
            return $item->kelas->program_studi_id ?? 'unknown';
        });

        foreach ($groupedJadwal as $prodiId => $items) {
            $prodi = $prodiId !== 'unknown' && $programStudis->has($prodiId) 
                ? $programStudis[$prodiId] 
                : (object)['nama' => 'Tanpa Program Studi'];

            $sheets[] = new JadwalUjianPerProdiSheet($prodi, $items);
        }

        return $sheets;
    }
}
