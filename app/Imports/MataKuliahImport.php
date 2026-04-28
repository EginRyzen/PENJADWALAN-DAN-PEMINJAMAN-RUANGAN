<?php

namespace App\Imports;

use App\Models\MasterDataMataKuliah;
use App\Models\MasterDataProgramStudi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MataKuliahImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $prodi = MasterDataProgramStudi::where('kode', $row['kode_prodi'])->first();

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

    public function rules(): array
    {
        return [
            'kode' => 'required',
            'nama' => 'required',
            'sks' => 'required|numeric',
            'semester' => 'required|numeric',
            'kode_prodi' => 'required|exists:master_data_program_studis,kode',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kode_prodi.exists' => 'Kode prodi ":input" tidak ditemukan.',
        ];
    }
}
