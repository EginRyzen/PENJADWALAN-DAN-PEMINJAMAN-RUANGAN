<?php

namespace App\Imports;

use App\Models\MasterDataHariLibur;
use App\Models\MasterDataPeriode;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class HariLiburImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $periode = MasterDataPeriode::where('nama', $row['nama_periode'])->first();

        $tanggal = $row['tanggal'];
        
        // Handle Excel numeric date serial
        if (is_numeric($tanggal)) {
            $tanggal = Carbon::instance(Date::excelToDateTimeObject($tanggal))->format('Y-m-d');
        } else {
            // Handle various string formats
            try {
                $tanggal = Carbon::parse($tanggal)->format('Y-m-d');
            } catch (\Exception $e) {
                // If it fails to parse, we'll let it be and it might fail in DB, 
                // but at least we tried common formats.
            }
        }

        return MasterDataHariLibur::updateOrCreate(
            ['tanggal' => $tanggal],
            [
                'periode_id' => $periode->id,
                'keterangan' => $row['keterangan'],
                'tipe'       => $row['tipe'] ?? 'nasional',
            ]
        );
    }

    public function rules(): array
    {
        return [
            'tanggal' => 'required',
            'keterangan' => 'required',
            'nama_periode' => 'required|exists:master_data_periodes,nama',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nama_periode.exists' => 'Nama periode ":input" tidak ditemukan.',
        ];
    }
}
