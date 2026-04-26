<?php

namespace App\Imports;

use App\Models\MasterDataKelas;
use App\Models\MasterDataProgramStudi;
use App\Models\MasterDataPeriode;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KelasImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $prodi = MasterDataProgramStudi::where('kode', $row['kode_prodi'])->first();
        $periode = MasterDataPeriode::where('nama', $row['nama_periode'])->first();

        if (!$prodi || !$periode) {
            return null;
        }

        return MasterDataKelas::updateOrCreate(
            [
                'nama_kelas'       => $row['nama_kelas'],
                'program_studi_id' => $prodi->id,
                'periode_id'       => $periode->id,
            ],
            []
        );
    }
}
