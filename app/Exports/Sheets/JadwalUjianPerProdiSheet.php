<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JadwalUjianPerProdiSheet implements FromArray, WithTitle, WithHeadings, ShouldAutoSize, WithStyles
{
    private $prodi;
    private $jadwal;
    private $groupRows = [];

    public function __construct($prodi, $jadwal)
    {
        $this->prodi = $prodi;
        $this->jadwal = $jadwal;
    }

    public function array(): array
    {
        $data = [];
        $currentDate = null;
        $rowCount = 1;

        foreach ($this->jadwal as $j) {
            $date = $j->tanggal ? $j->tanggal->format('Y-m-d') : 'Tanpa Tanggal';
            
            // Format tanggal yang bagus, e.g. "Senin, 17-05-2026"
            $hariMap = [
                'monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu', 
                'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu', 'sunday' => 'Minggu',
                'senin' => 'Senin', 'selasa' => 'Selasa', 'rabu' => 'Rabu', 
                'kamis' => 'Kamis', 'jumat' => 'Jumat', 'sabtu' => 'Sabtu', 'minggu' => 'Minggu'
            ];
            $hariRaw = strtolower($j->hari ?? '');
            $hari = $hariMap[$hariRaw] ?? ucfirst($hariRaw);
            
            $displayDate = $j->tanggal ? $hari . ', ' . $j->tanggal->format('d-m-Y') : 'Tanpa Tanggal';
            
            if ($currentDate !== $date) {
                $rowCount++;
                $this->groupRows[] = $rowCount;
                
                $data[] = [
                    'TANGGAL: ' . strtoupper($displayDate),
                    '', '', '', '', '', '', ''
                ];
                $currentDate = $date;
            }

            $rowCount++;
            
            $statusText = 'OK';
            if ($j->status_konflik === 'conflict') $statusText = 'KONFLIK';
            if ($j->status_konflik === 'edited') $statusText = 'DIEDIT';

            $data[] = [
                $j->jam_mulai . ' - ' . $j->jam_selesai,
                strtoupper($j->mataKuliah->nama ?? '-'),
                $j->mataKuliah->sks ?? '-',
                $j->kelas->nama_kelas ?? '-',
                $j->dosen->nama ?? '-',
                $j->ruangan->room_name ?? '-',
                $j->ruangan->room_capacity ?? '-',
                $statusText
            ];
        }

        return $data;
    }

    public function title(): string
    {
        $nama = $this->prodi->nama ?? 'Tanpa Program Studi';
        // Excel tab names cannot be longer than 31 characters and cannot contain certain characters
        $safeTitle = str_replace(['*', ':', '?', '[', ']', '\\', '/'], '', $nama);
        return substr($safeTitle, 0, 31);
    }

    public function headings(): array
    {
        return [
            'WAKTU',
            'MATA KULIAH',
            'SKS',
            'KELAS',
            'PENGAWAS / DOSEN',
            'RUANGAN',
            'KAPASITAS',
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
            ],
        ];

        foreach ($this->groupRows as $row) {
            $sheet->mergeCells("A{$row}:H{$row}");
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

        $lastRow = count($this->array()) + 1; // +1 for heading
        if ($lastRow > 1) {
            $sheet->getStyle("A1:H{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            // Center alignment for some columns
            $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C2:D{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G2:H{$lastRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        return $styles;
    }
}
