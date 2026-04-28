<?php

namespace App\Imports;

use App\Models\MasterDataKelas;
use App\Models\MasterDataMataKuliah;
use App\Models\MasterDataKelasMataKuliah;
use App\Models\MasterDataProgramStudi;
use App\Models\MasterDataPeriode;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class KelasMataKuliahImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $prodi = MasterDataProgramStudi::where('kode', $row['kode_prodi'])->first();
        $periode = MasterDataPeriode::where('nama', $row['nama_periode'])->first();
        
        $kelas = MasterDataKelas::where([
            'nama_kelas' => $row['nama_kelas'],
            'program_studi_id' => $prodi->id,
            'periode_id' => $periode->id,
        ])->first();

        $mataKuliah = MasterDataMataKuliah::where('kode', $row['kode_matkul'])->first();

        return MasterDataKelasMataKuliah::updateOrCreate(
            [
                'kelas_id'       => $kelas->id,
                'mata_kuliah_id' => $mataKuliah->id,
            ],
            [
                'semester'       => $row['semester'],
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nama_kelas'   => 'required',
            'kode_prodi'   => 'required|exists:master_data_program_studis,kode',
            'nama_periode' => 'required|exists:master_data_periodes,nama',
            'kode_matkul'  => 'required|exists:master_data_mata_kuliahs,kode',
            'semester'     => 'required|numeric',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kode_prodi.exists'   => 'Kode prodi ":input" tidak ditemukan.',
            'nama_periode.exists' => 'Nama periode ":input" tidak ditemukan.',
            'kode_matkul.exists'  => 'Kode mata kuliah ":input" tidak ditemukan.',
        ];
    }
}
