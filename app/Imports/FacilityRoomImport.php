<?php

namespace App\Imports;

use App\Models\BuildingFacilityRoom;
use App\Models\DataBaseBuildingFacility;
use App\Models\DataBaseBuildingRoom;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FacilityRoomImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $room = DataBaseBuildingRoom::where('room_code', $row['room_code'])->first();
        if (!$room) {
            return null;
        }

        $facility = DataBaseBuildingFacility::firstOrCreate(
            ['facility_name' => $row['facility_name']]
        );

        return BuildingFacilityRoom::updateOrCreate(
            [
                'room_id'     => $room->id,
                'facility_id' => $facility->id,
            ],
            [
                'quantity' => $row['quantity'] ?? 1,
            ]
        );
    }
}
