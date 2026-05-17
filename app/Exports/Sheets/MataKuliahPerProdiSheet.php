<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MataKuliahPerProdiSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    private $prodi;
    private $mataKuliahs;
    private $groupRows = [];

    public function __construct($prodi, $mataKuliahs)
    {
        $this->prodi = $prodi;
        $this->mataKuliahs = $mataKuliahs;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $data = [];
        $currentSemester = null;
        $rowCount = 1; // Starting from 1 for headings

        foreach ($this->mataKuliahs as $mk) {
            $semester = $mk->semester ?? 'Lainnya';
            
            if ($currentSemester !== $semester) {
                $rowCount++;
                $this->groupRows[] = $rowCount;
                
                // Add a grouping header row
                $data[] = [
                    'SEMESTER: ' . $semester,
                    '', '', '', '', ''
                ];
                $currentSemester = $semester;
            }

            $rowCount++;
            $data[] = [
                $mk->kode,
                strtoupper($mk->nama),
                $mk->sks,
                $mk->semester,
                $mk->sks_ujian,
                $mk->programStudi->nama ?? '-'
            ];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return substr($this->prodi->nama, 0, 31);
    }

    public function headings(): array
    {
        return [
            'KODE MK',
            'NAMA MATA KULIAH',
            'SKS',
            'SEMESTER',
            'SKS UJIAN',
            'PROGRAM STUDI'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4F81BD'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ],
        ];

        // Style the group header rows
        foreach ($this->groupRows as $row) {
            $sheet->mergeCells("A{$row}:F{$row}");
            $styles[$row] = [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FF000000'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE9ECEF'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ],
            ];
        }

        return $styles;
    }
}
