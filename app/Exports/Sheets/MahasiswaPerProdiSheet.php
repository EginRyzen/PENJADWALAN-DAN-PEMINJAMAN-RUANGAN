<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MahasiswaPerProdiSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    private $prodi;
    private $mahasiswas;
    private $groupRows = [];

    public function __construct($prodi, $mahasiswas)
    {
        $this->prodi = $prodi;
        $this->mahasiswas = $mahasiswas;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $data = [];
        $currentKelas = null;
        $rowCount = 1; // Starting from 1 for headings

        foreach ($this->mahasiswas as $mahasiswa) {
            $kelasNama = $mahasiswa->kelas->nama_kelas ?? 'Tanpa Kelas';
            
            if ($currentKelas !== $kelasNama) {
                $rowCount++;
                $this->groupRows[] = $rowCount;
                
                // Add a grouping header row
                $data[] = [
                    'KELAS: ' . strtoupper($kelasNama),
                    '', '', '', '', ''
                ];
                $currentKelas = $kelasNama;
            }

            $rowCount++;
            $data[] = [
                $mahasiswa->nim,
                strtoupper($mahasiswa->nama),
                $mahasiswa->programStudi->nama ?? '-',
                $mahasiswa->kelas->nama_kelas ?? '-',
                $mahasiswa->periode->nama ?? '-',
                $mahasiswa->status
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
            'NIM',
            'NAMA MAHASISWA',
            'PROGRAM STUDI',
            'KELAS',
            'PERIODE MASUK',
            'STATUS'
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
