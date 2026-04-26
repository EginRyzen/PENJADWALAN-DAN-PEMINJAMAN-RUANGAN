<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BuildingTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new GenericTemplateExport('Gedung', [
                ['building_name', 'building_code', 'building_location', 'building_status'],
                ['Gedung A', 'GD-A', 'Kampus 1', 'active'],
            ]),
            new GenericTemplateExport('Ruangan', [
                ['building_code', 'room_name', 'room_code', 'room_location', 'room_status', 'room_capacity', 'room_purpose', 'can_ujian', 'can_pembelajaran'],
                ['GD-A', 'Ruang 101', 'R101', 'Lantai 1', 'active', 40, 'Kelas', 'true', 'true'],
            ]),
            new GenericTemplateExport('Fasilitas Ruangan', [
                ['room_code', 'facility_name', 'quantity'],
                ['R101', 'Projector', 1],
                ['R101', 'AC', 2],
            ]),
        ];
    }
}
