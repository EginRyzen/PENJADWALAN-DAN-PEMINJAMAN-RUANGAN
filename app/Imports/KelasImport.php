<?php

namespace App\Imports;

use App\Models\MasterDataKelas;
use App\Models\MasterDataProgramStudi;
use App\Models\MasterDataPeriode;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class KelasImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $prodi = MasterDataProgramStudi::where('kode', $row['kode_prodi'])->first();
        $periode = MasterDataPeriode::where('nama', $row['nama_periode'])->first();

        return MasterDataKelas::updateOrCreate(
            [
                'nama_kelas'       => $row['nama_kelas'],
                'program_studi_id' => $prodi->id,
                'periode_id'       => $periode->id,
            ],
            []
        );
    }

    public function rules(): array
    {
        return [
            'nama_kelas' => 'required',
            'kode_prodi' => 'required|exists:master_data_program_studis,kode',
            'nama_periode' => 'required|exists:master_data_periodes,nama',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kode_prodi.exists' => 'Kode prodi ":input" tidak ditemukan.',
            'nama_periode.exists' => 'Nama periode ":input" tidak ditemukan.',
        ];
    }
}
