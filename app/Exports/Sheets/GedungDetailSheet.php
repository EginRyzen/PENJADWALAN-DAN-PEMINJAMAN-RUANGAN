<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GedungDetailSheet implements FromArray, WithTitle, ShouldAutoSize, WithStyles
{
    private $building;

    public function __construct($building)
    {
        $this->building = $building;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $data = [];

        // 1. Building Header Info
        $data[] = ['DATA GEDUNG: ' . strtoupper($this->building->building_name)];
        $data[] = [''];
        $data[] = ['Kode Gedung', ': ' . $this->building->building_code];
        $data[] = ['Lokasi', ': ' . $this->building->building_location];
        $data[] = ['Status', ': ' . strtoupper($this->building->building_status)];
        $data[] = [''];
        $data[] = [''];

        // 2. Room Table Header
        $data[] = ['DAFTAR RUANGAN DAN FASILITAS'];
        $data[] = [
            'No',
            'Kode Ruangan',
            'Nama Ruangan',
            'Lokasi',
            'Kapasitas',
            'Kegunaan',
            'Ujian?',
            'Belajar?',
            'Fasilitas (Nama & Jumlah)'
        ];

        // 3. Room Data
        $rooms = $this->building->rooms;
        foreach ($rooms as $index => $room) {
            $facilities = $room->facilities->map(function($f) {
                return ($f->facility->facility_name ?? '-') . ' (' . $f->quantity . ')';
            })->implode(', ');

            $data[] = [
                $index + 1,
                $room->room_code,
                $room->room_name,
                $room->room_location,
                $room->room_capacity,
                $room->room_purpose,
                $room->can_ujian ? 'Ya' : 'Tidak',
                $room->can_pembelajaran ? 'Ya' : 'Tidak',
                $facilities ?: '-'
            ];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        // Sheet title must be less than 31 characters
        return substr($this->building->building_name, 0, 31);
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [];

        // Style Building Title
        $sheet->mergeCells('A1:I1');
        $styles[1] = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];

        // Style Info Labels
        foreach ([3, 4, 5] as $row) {
            $styles["A{$row}"] = ['font' => ['bold' => true]];
        }

        // Style Table Title
        $sheet->mergeCells('A8:I8');
        $styles[8] = [
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE9ECEF'],
            ],
        ];

        // Style Table Header
        $styles[9] = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF4F81BD'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];

        // Apply borders to table rows
        $lastRow = count($this->array());
        if ($lastRow >= 10) {
            $sheet->getStyle("A10:I{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("A10:I{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            
            // Center align some columns
            $sheet->getStyle("A10:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E10:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G10:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return $styles;
    }
}
