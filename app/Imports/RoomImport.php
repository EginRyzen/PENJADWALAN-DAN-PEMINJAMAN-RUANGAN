<?php

namespace App\Imports;

use App\Models\DataBaseBuilding;
use App\Models\DataBaseBuildingRoom;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RoomImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $building = DataBaseBuilding::where('building_code', $row['building_code'])->first();

        if (!$building) {
            return null; // Or handle error: Building not found
        }

        return DataBaseBuildingRoom::updateOrCreate(
            ['room_code' => $row['room_code']],
            [
                'building_id'      => $building->id,
                'room_name'        => $row['room_name'],
                'room_location'    => $row['room_location'] ?? '-',
                'room_status'      => $row['room_status'] ?? 'inactive',
                'room_capacity'    => $row['room_capacity'] ?? 0,
                'room_purpose'     => $row['room_purpose'] ?? '',
                'can_ujian'        => filter_var($row['can_ujian'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'can_pembelajaran' => filter_var($row['can_pembelajaran'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ]
        );
    }
}
