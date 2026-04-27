<?php

namespace App\Imports;

use App\Models\MasterDataProgramStudi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProgramStudiImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return MasterDataProgramStudi::updateOrCreate(
            ['kode' => $row['kode']],
            [
                'nama'     => $row['nama'],
                'fakultas' => $row['fakultas'] ?? 'Kampus 5 PSDKU',
                'jenjang'  => $row['jenjang'],
                'status'   => $row['status'] ?? 'aktif',
            ]
        );
    }
}
