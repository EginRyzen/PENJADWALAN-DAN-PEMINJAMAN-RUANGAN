<?php

namespace App\Exports;

use App\Models\MasterDataKelasMataKuliah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KelasMataKuliahExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = MasterDataKelasMataKuliah::with(['kelas', 'mataKuliah.programStudi']);

        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('kelas', function($k) use ($search) {
                    $k->where('nama_kelas', 'like', "%{$search}%");
                })->orWhereHas('mataKuliah', function($m) use ($search) {
                    $m->where('nama', 'like', "%{$search}%");
                });
            });
        }

        if (!empty($this->filters['kelas_id'])) {
            $query->where('kelas_id', $this->filters['kelas_id']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nama Kelas',
            'Kode Mata Kuliah',
            'Nama Mata Kuliah',
            'SKS',
            'Semester Plotting',
            'Program Studi'
        ];
    }

    public function map($item): array
    {
        return [
            $item->kelas->nama_kelas ?? '-',
            $item->mataKuliah->kode ?? '-',
            $item->mataKuliah->nama ?? '-',
            $item->mataKuliah->sks ?? '-',
            $item->semester,
            $item->mataKuliah->programStudi->nama ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text with white font and blue background
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
    }
}
