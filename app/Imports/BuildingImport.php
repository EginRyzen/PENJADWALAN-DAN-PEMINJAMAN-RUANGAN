<?php

namespace App\Imports;

use App\Models\DataBaseBuilding;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BuildingImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return DataBaseBuilding::updateOrCreate(
            ['building_code' => $row['building_code']],
            [
                'building_name'     => $row['building_name'],
                'building_location' => $row['building_location'] ?? '-',
                'building_status'   => $row['building_status'] ?? 'inactive',
            ]
        );
    }
}
