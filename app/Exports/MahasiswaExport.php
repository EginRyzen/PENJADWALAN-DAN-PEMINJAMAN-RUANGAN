<?php

namespace App\Exports;

use App\Models\MasterDataProgramStudi;
use App\Models\MasterDataMahasiswa;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\MahasiswaPerProdiSheet;

class MahasiswaExport implements WithMultipleSheets
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        // Get all study programs
        $programStudis = MasterDataProgramStudi::all();

        foreach ($programStudis as $prodi) {
            $query = MasterDataMahasiswa::with(['programStudi', 'kelas', 'periode'])
                ->where('master_data_mahasiswas.program_studi_id', $prodi->id);

            if (!empty($this->filters['search'])) {
                $search = $this->filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('master_data_mahasiswas.nama', 'like', "%{$search}%")
                      ->orWhere('master_data_mahasiswas.nim', 'like', "%{$search}%");
                });
            }

            if (!empty($this->filters['status'])) {
                $query->where('master_data_mahasiswas.status', $this->filters['status']);
            }

            $mahasiswas = $query->join('master_data_kelas', 'master_data_mahasiswas.kelas_id', '=', 'master_data_kelas.id')
                ->orderBy('master_data_kelas.nama_kelas', 'asc')
                ->orderBy('master_data_mahasiswas.nama', 'asc')
                ->select('master_data_mahasiswas.*')
                ->get();

            // Only add sheet if there are students in that prodi
            if ($mahasiswas->count() > 0) {
                $sheets[] = new MahasiswaPerProdiSheet($prodi, $mahasiswas);
            }
        }

        return $sheets;
    }
}
