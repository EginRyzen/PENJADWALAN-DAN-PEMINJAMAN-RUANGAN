<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetBuildingImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Gedung'            => new BuildingImport(),
            'Ruangan'           => new RoomImport(),
            'Fasilitas Ruangan' => new FacilityRoomImport(),
        ];
    }
}
