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
            new GenericSheetExport('Gedung', [
                ['building_name', 'building_code', 'building_location', 'building_status'],
                ['Gedung A', 'GD-A', 'Kampus 1', 'active'],
            ]),
            new GenericSheetExport('Ruangan', [
                ['building_code', 'room_name', 'room_code', 'room_location', 'room_status', 'room_capacity', 'room_purpose', 'can_ujian', 'can_pembelajaran'],
                ['GD-A', 'Ruang 101', 'R101', 'Lantai 1', 'active', 40, 'Kelas', 'true', 'true'],
            ]),
            new GenericSheetExport('Fasilitas Ruangan', [
                ['room_code', 'facility_name', 'quantity'],
                ['R101', 'Projector', 1],
                ['R101', 'AC', 2],
            ]),
        ];
    }
}

class GenericSheetExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    private $title;
    private $data;

    public function __construct($title, $data)
    {
        $this->title = $title;
        $this->data = $data;
    }

    public function collection()
    {
        return collect(array_slice($this->data, 1));
    }

    public function headings(): array
    {
        return $this->data[0];
    }

    public function title(): string
    {
        return $this->title;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text with background color
            1    => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD'],
                ],
            ],
        ];
    }
}
