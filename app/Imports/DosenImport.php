<?php

namespace App\Imports;

use App\Models\MasterDataDosen;
use App\Models\MasterDataProgramStudi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DosenImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $prodi = MasterDataProgramStudi::where('kode', $row['kode_prodi'])->first();

        return MasterDataDosen::updateOrCreate(
            ['nidn' => $row['nidn']],
            [
                'nip'              => $row['nip'] ?? null,
                'nama'             => $row['nama'],
                'program_studi_id' => $prodi->id,
                'jabatan'          => $row['jabatan'],
                'status'           => $row['status'] ?? 'Aktif',
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nidn' => 'required',
            'nama' => 'required',
            'kode_prodi' => 'required|exists:master_data_program_studis,kode',
            'jabatan' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kode_prodi.exists' => 'Kode prodi ":input" tidak ditemukan.',
        ];
    }
}
