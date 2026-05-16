<?php

namespace App\Exports;

use App\Models\MasterDataProgramStudi;
use App\Models\MasterDataMataKuliah;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\MataKuliahPerProdiSheet;

class MataKuliahExport implements WithMultipleSheets
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
            $query = MasterDataMataKuliah::with('programStudi')
                ->where('program_studi_id', $prodi->id);

            if (!empty($this->filters['search'])) {
                $search = $this->filters['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('kode', 'like', "%{$search}%");
                });
            }

            $mataKuliahs = $query->orderBy('semester', 'asc')
                ->orderBy('nama', 'asc')
                ->get();

            // Only add sheet if there are courses in that prodi
            if ($mataKuliahs->count() > 0) {
                $sheets[] = new MataKuliahPerProdiSheet($prodi, $mataKuliahs);
            }
        }

        return $sheets;
    }
}
