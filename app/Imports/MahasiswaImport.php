<?php

namespace App\Imports;

use App\Models\MasterDataMahasiswa;
use App\Models\MasterDataProgramStudi;
use App\Models\MasterDataKelas;
use App\Models\MasterDataPeriode;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MahasiswaImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $prodi = MasterDataProgramStudi::where('kode', $row['kode_prodi'])->first();
        $periode = MasterDataPeriode::where('nama', $row['nama_periode'])->first();
        $kelas = MasterDataKelas::where('nama_kelas', $row['nama_kelas'])->first();

        return MasterDataMahasiswa::updateOrCreate(
            ['nim' => $row['nim']],
            [
                'nama'             => $row['nama'],
                'program_studi_id' => $prodi->id,
                'kelas_id'         => $kelas ? $kelas->id : null,
                'semester'         => $row['semester'],
                'periode_id'       => $periode->id,
                'status'           => $row['status'] ?? 'Aktif',
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nim' => 'required',
            'nama' => 'required',
            'kode_prodi' => 'required|exists:master_data_program_studis,kode',
            'nama_periode' => 'required|exists:master_data_periodes,nama',
            'nama_kelas' => 'nullable|exists:master_data_kelas,nama_kelas',
            'semester' => 'required|numeric',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kode_prodi.exists' => 'Kode prodi ":input" tidak ditemukan.',
            'nama_periode.exists' => 'Nama periode ":input" tidak ditemukan.',
            'nama_kelas.exists' => 'Nama kelas ":input" tidak ditemukan.',
        ];
    }
}
