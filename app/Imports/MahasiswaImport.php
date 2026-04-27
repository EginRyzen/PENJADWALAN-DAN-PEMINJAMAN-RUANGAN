<?php

namespace App\Imports;

use App\Models\MasterDataMahasiswa;
use App\Models\MasterDataProgramStudi;
use App\Models\MasterDataKelas;
use App\Models\MasterDataPeriode;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MahasiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $prodi = MasterDataProgramStudi::where('kode', $row['kode_prodi'])->first();
        $periode = MasterDataPeriode::where('nama', $row['nama_periode'])->first();
        $kelas = MasterDataKelas::where('nama_kelas', $row['nama_kelas'])->first();

        if (!$prodi || !$periode) {
            return null;
        }

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
}
