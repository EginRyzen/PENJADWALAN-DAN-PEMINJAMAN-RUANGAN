<?php

namespace App\Exports;

use App\Models\PengajuanRuangan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class PengajuanPeminjamanExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $request = $this->request;
        $query = PengajuanRuangan::with(['status', 'user', 'items.ruangan.building']);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('no_pengajuan', 'like', '%' . $request->search . '%')
                  ->orWhere('alasan', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->tipe) {
            $query->whereIn('tipe_pengajuan', explode(',', $request->tipe));
        }

        if ($request->status) {
            $query->whereHas('status', function($q) use ($request) {
                $q->whereIn('nama_status', explode(',', $request->status));
            });
        }

        if ($request->buildings) {
            $query->whereHas('items.ruangan', function($q) use ($request) {
                $q->whereIn('building_id', explode(',', $request->buildings));
            });
        }

        if ($request->rooms) {
            $query->whereHas('items', function($q) use ($request) {
                $q->whereIn('ruangan_id', explode(',', $request->rooms));
            });
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No. Pengajuan',
            'Peminjam',
            'Tipe Pengajuan',
            'Status',
            'Gedung',
            'Daftar Ruangan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Jam Mulai',
            'Jam Selesai',
            'Alasan/Keterangan',
            'Waktu Pembuatan'
        ];
    }

    public function map($row): array
    {
        $rooms = $row->items->map(function($item) {
            return $item->ruangan ? $item->ruangan->room_name : '';
        })->filter()->implode(', ');

        $buildings = $row->items->map(function($item) {
            return ($item->ruangan && $item->ruangan->building) ? $item->ruangan->building->building_code : '';
        })->unique()->filter()->implode(', ');

        return [
            $row->no_pengajuan,
            $row->user ? $row->user->name : '-',
            $row->tipe_pengajuan,
            $row->status ? $row->status->nama_status : '-',
            $buildings,
            $rooms,
            $row->tanggal_start_peminjaman,
            $row->tanggal_end_peminjaman,
            $row->jam_mulai,
            $row->jam_selesai,
            $row->alasan,
            $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : '-',
        ];
    }

    public function title(): string
    {
        return 'Laporan Peminjaman Ruangan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '14B8A6'], // Teal 500
                ],
            ],
        ];
    }
}
