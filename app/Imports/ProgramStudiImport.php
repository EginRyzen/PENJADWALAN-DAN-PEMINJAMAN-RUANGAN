<?php

namespace App\Imports;

use App\Models\MasterDataProgramStudi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProgramStudiImport implements ToModel, WithHeadingRow, WithValidation
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

    public function rules(): array
    {
        return [
            'kode' => 'required',
            'nama' => 'required',
            'jenjang' => 'required',
        ];
    }
}
