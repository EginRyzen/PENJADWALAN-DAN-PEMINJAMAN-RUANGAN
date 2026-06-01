<?php

namespace App\Exports;

use App\Models\Role;
use App\Models\Menu;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RoleMenuExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $allMenus;

    public function __construct()
    {
        // Mengambil semua menu yang ada untuk dijadikan sebagai kolom matriks (header)
        // Diurutkan berdasarkan sequence agar sesuai struktur aslinya
        $this->allMenus = Menu::orderBy('sequence', 'asc')->get();
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Role::with(['menus'])->orderBy('name_role', 'asc')->get();
    }

    public function headings(): array
    {
        // Menyiapkan header kolom pertama dan kedua
        $headers = [
            'No',
            'Role (Nama Peran)'
        ];

        // Header selanjutnya adalah nama-nama menu yang tersedia
        foreach ($this->allMenus as $menu) {
            $headers[] = $menu->menu_name;
        }

        return $headers;
    }

    public function map($role): array
    {
        static $rowNumber = 1;
        
        // Mengumpulkan daftar ID menu yang dimiliki oleh role ini
        $assignedMenuIds = $role->menus->pluck('id')->toArray();

        // Mengisi kolom statis
        $row = [
            $rowNumber++,
            $role->name_role,
        ];

        // Mengisi matriks akses untuk setiap menu
        foreach ($this->allMenus as $menu) {
            // Jika role punya ID menu ini, tampilkan ✓, jika tidak tampilkan silang
            $row[] = in_array($menu->id, $assignedMenuIds) ? '✓ Akses' : '✗ Tidak';
        }

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();

        // Style untuk Header Row (Baris 1)
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF0D9488'], // Menggunakan warna teal (seperti UI frontend)
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style untuk seluruh tabel (Border)
        $sheet->getStyle('A1:' . $lastCol . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FFDDDDDD'],
                ],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style untuk text alignment:
        // - Kolom No (A) => Center
        // - Kolom Role (B) => Left
        // - Kolom Menu (C sampai habis) => Center
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B2:B' . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        
        if ($lastCol !== 'A' && $lastCol !== 'B') {
            $sheet->getStyle('C2:' . $lastCol . $lastRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Mewarnai teks & background seperti Badge
            $highestColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastCol);
            
            for ($row = 2; $row <= $lastRow; $row++) {
                for ($colIndex = 3; $colIndex <= $highestColIndex; $colIndex++) {
                    $colStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                    $cellCoordinate = $colStr . $row;
                    $val = $sheet->getCell($cellCoordinate)->getValue();
                    
                    if ($val === '✓ Akses') {
                        $sheet->getStyle($cellCoordinate)->applyFromArray([
                            'font' => [
                                'color' => ['argb' => 'FF0F766E'], // Teal 700 (Hijau Gelap)
                                'bold' => true,
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFCCFBF1'], // Teal 100 (Hijau Terang/Soft)
                            ],
                        ]);
                    } elseif ($val === '✗ Tidak') {
                        $sheet->getStyle($cellCoordinate)->applyFromArray([
                            'font' => [
                                'color' => ['argb' => 'FFEF4444'], // Red 500 (Merah)
                            ],
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['argb' => 'FFFEE2E2'], // Red 100 (Merah Terang/Soft)
                            ],
                        ]);
                    }
                }
            }
        }

        return [];
    }
}
