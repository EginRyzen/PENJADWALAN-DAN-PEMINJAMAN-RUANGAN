<?php

namespace App\Imports;

use App\Models\MasterDataHariLibur;
use App\Models\MasterDataPeriode;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HariLiburImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $periode = MasterDataPeriode::where('nama', $row['nama_periode'])->first();

        if (!$periode) {
            return null;
        }

        return MasterDataHariLibur::updateOrCreate(
            ['tanggal' => $row['tanggal']],
            [
                'periode_id' => $periode->id,
                'keterangan' => $row['keterangan'],
                'tipe'       => $row['tipe'] ?? 'nasional',
            ]
        );
    }
}
