<?php

namespace App\Imports;

use App\Models\MasterDataMataKuliah;
use App\Models\MasterDataProgramStudi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MataKuliahImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $prodi = MasterDataProgramStudi::where('kode', $row['kode_prodi'])->first();

        if (!$prodi) {
            return null;
        }

        return MasterDataMataKuliah::updateOrCreate(
            ['kode' => $row['kode']],
            [
                'nama'             => $row['nama'],
                'sks'              => $row['sks'],
                'semester'         => $row['semester'],
                'sks_ujian'        => $row['sks_ujian'] ?? 0,
                'program_studi_id' => $prodi->id,
            ]
        );
    }
}
