<?php

namespace App\Imports;

use App\Models\MasterDataDosen;
use App\Models\MasterDataProgramStudi;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DosenImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $prodi = MasterDataProgramStudi::where('kode', $row['kode_prodi'])->first();

        if (!$prodi) {
            return null;
        }

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
}
