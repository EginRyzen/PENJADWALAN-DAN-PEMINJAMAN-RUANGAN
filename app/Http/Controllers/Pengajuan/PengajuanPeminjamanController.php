<?php

namespace App\Http\Controllers\Pengajuan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengajuan\StorePengajuanPeminjamanRequest;
use App\Models\DataBaseBuilding;
use App\Models\DataBaseBuildingRoom;
use App\Models\DataDocument;
use App\Models\PengajuanHistory;
use App\Models\PengajuanRuangan;
use App\Models\PengajuanRuanganItem;
use App\Models\WorkflowStep;
use App\Models\User;
use App\Notifications\NewPengajuanNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PengajuanPeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = PengajuanRuangan::with(['status', 'user', 'items.ruangan']);

            // Filter Pencarian (No. Pengajuan atau Alasan)
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('no_pengajuan', 'like', '%' . $request->search . '%')
                      ->orWhere('alasan', 'like', '%' . $request->search . '%');
                });
            }

            // Filter Tipe Pengajuan
            if ($request->tipe) {
                $query->whereIn('tipe_pengajuan', explode(',', $request->tipe));
            }

            // Filter Gedung (melalui relasi items -> ruangan)
            if ($request->buildings) {
                $query->whereHas('items.ruangan', function($q) use ($request) {
                    $q->whereIn('building_id', explode(',', $request->buildings));
                });
            }

            // Filter Rentang Tanggal
            if ($request->start_date && $request->end_date) {
                $query->whereBetween('created_at', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            }

            $size = $request->size ?? 10;
            $page = $request->page !== null ? ((int) $request->page) + 1 : 1;
            $data = $query->orderBy('created_at', 'desc')->paginate($size, ['*'], 'page', $page);

            // Map data untuk menambahkan properti 'ruangan' di level atas (mengambil item pertama)
            // Ini mempermudah integrasi dengan komponen UI yang ada
            $items = collect($data->items())->map(function($item) {
                $itemArray = $item->toArray();
                $firstItem = $item->items->first();
                $itemArray['ruangan'] = $firstItem ? $firstItem->ruangan : null;
                return $itemArray;
            });

            return response()->json([
                'result' => $items,
                'pagination' => [
                    'current_page' => $data->currentPage() - 1, // 0-based index untuk frontend
                    'total_elements' => $data->total(),
                    'total_elements_per_page' => (int) $data->perPage(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengambil data pengajuan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePengajuanPeminjamanRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                // 1. Get the primary role of the user (MAHASISWA or DOSEN)
                $user = auth()->user();
                $userRole = $user->roles()->whereIn('name_role', ['MAHASISWA', 'DOSEN'])->first();
                
                if (!$userRole) {
                   return response()->json(['message' => 'Anda tidak memiliki otoritas untuk membuat pengajuan.'], 403);
                }

                $validated = $request->validated();
                $roomIds = $validated['all_room_ids'];

                // 2. Conflict Check (Ignore REJECTED/TOLAK/DITOLAK)
                $hasConflict = PengajuanRuangan::whereHas('items', function($q) use ($roomIds) {
                        $q->whereIn('ruangan_id', $roomIds);
                    })
                    ->whereHas('status', function($q) {
                        $q->where('nama_status', 'not like', '%REJECT%')
                          ->where('nama_status', 'not like', '%TOLAK%')
                          ->where('nama_status', 'not like', '%DITOLAK%');
                    })
                    ->where(function($q) use ($validated) {
                        $q->where('tanggal_start_peminjaman', '<=', $validated['tanggal_end'])
                          ->where('tanggal_end_peminjaman', '>=', $validated['tanggal_start']);
                    })
                    ->where(function($q) use ($validated) {
                        $q->where('jam_mulai', '<', $validated['jam_selesai'])
                          ->where('jam_selesai', '>', $validated['jam_mulai']);
                    })
                    ->exists();

                if ($hasConflict) {
                    return response()->json(['message' => 'Salah satu ruangan sudah dipesan pada waktu tersebut.'], 422);
                }

                // 3. Determine Initial Workflow status (Next sequence after draft)
                $userRoleName = $userRole->name_role;
                $statusName = '';
                
                if ($userRoleName === 'MAHASISWA') {
                    $statusName = ($validated['tipe_pengajuan'] === 'PEMBELAJARAN') ? 'VERIFIKASI_TU' : 'VALIDASI_KEMAHASISWAAN';
                } else if ($userRoleName === 'DOSEN') {
                    $statusName = ($validated['tipe_pengajuan'] === 'PEMBELAJARAN') ? 'VERIFIKASI_TU' : 'PENGECEKAN_RUANG_TU';
                }

                $nextStatus = WorkflowStep::where('tipe_pengajuan', $validated['tipe_pengajuan'])
                    ->where('urutan', 2)
                    ->where('nama_status', $statusName)
                    ->first();

                $initialStep = WorkflowStep::where('tipe_pengajuan', $validated['tipe_pengajuan'])
                    ->where('role_id', $userRole->id)
                    ->where('urutan', 1)
                    ->first();

                // 4. Generate No. Pengajuan (Format: KodeGedung-Tahun-Urutan)
                $firstGedungId = $validated['items'][0]['building_id'];
                $gedung = DataBaseBuilding::findOrFail($firstGedungId);
                $year = date('Y');
                
                $prefix = $gedung->building_code . '-' . $year . '-';
                $lastRecord = PengajuanRuangan::where('no_pengajuan', 'like', $prefix . '%')
                    ->orderBy('no_pengajuan', 'desc')
                    ->first();

                if ($lastRecord) {
                    // Extract sequence from "CODE-YEAR-NNNN"
                    $lastSequence = (int) substr($lastRecord->no_pengajuan, -4);
                    $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $sequence = '0001';
                }
                
                $noPengajuan = $prefix . $sequence;

                // 5. Create Pengajuan
                $pengajuan = PengajuanRuangan::create([
                    'no_pengajuan' => $noPengajuan,
                    'tipe_pengajuan' => $validated['tipe_pengajuan'],
                    'current_status_id' => $nextStatus ? $nextStatus->id : ($initialStep ? $initialStep->id : null),
                    'user_id' => $user->id,
                    'tanggal_pengajuan' => now(),
                    'tanggal_start_peminjaman' => $validated['tanggal_start'],
                    'tanggal_end_peminjaman' => $validated['tanggal_end'],
                    'jam_mulai' => $validated['jam_mulai'],
                    'jam_selesai' => $validated['jam_selesai'],
                    'alasan' => $validated['alasan'],
                    'dokumen_pendukung_id' => $validated['dokumen_pendukung_id'] ?? null,
                ]);

                // 7. Create Items
                foreach ($roomIds as $roomId) {
                    PengajuanRuanganItem::create([
                        'pengajuan_id' => $pengajuan->id,
                        'ruangan_id' => $roomId,
                    ]);
                }

                // 8. Create Histories
                // Entry 1: Created (Draft status)
                PengajuanHistory::create([
                    'pengajuan_id' => $pengajuan->id,
                    'status_id' => $initialStep ? $initialStep->id : $pengajuan->current_status_id,
                    'user_id' => $user->id,
                    'aksi' => 'CREATED',
                    // 'catatan' => 'Membuat draft pengajuan',
                    'sequence' => 1,
                ]);

                // Entry 2: Submitted (Current Status)
                if ($nextStatus) {
                    PengajuanHistory::create([
                        'pengajuan_id' => $pengajuan->id,
                        'status_id' => $pengajuan->current_status_id,
                        'user_id' => $user->id,
                        'aksi' => 'SUBMITTED',
                        // 'catatan' => 'Mengajukan peminjaman ruangan',
                        'sequence' => 2,
                    ]);
                }

                // 9. Send Notification to next approvers based on workflow
                if ($nextStatus && $nextStatus->role) {
                    try {
                        // Ambil semua user yang memiliki role dari status berikutnya
                        $nextApprovers = $nextStatus->role->users;

                        if ($nextApprovers && $nextApprovers->count() > 0) {
                            foreach ($nextApprovers as $approver) {
                                try {
                                    $approver->notify(new NewPengajuanNotification($pengajuan, $user));
                                } catch (\Exception $e) {
                                    // Log error khusus untuk user ini agar tidak menghentikan notifikasi ke user lain
                                    logger()->error('Gagal mengirim notifikasi ke: ' . $approver->email . ' Error: ' . $e->getMessage());
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        logger()->error('Notification Error: ' . $e->getMessage());
                        // Don't throw error, allow booking to succeed even if notification fails
                    }
                }

                return response()->json([
                    'message' => 'Pengajuan berhasil dibuat.',
                    'data' => $pengajuan
                ], 201);
            });
        } catch (\Exception $e) {
            // Log the actual error for debugging
            logger()->error('Pengajuan Error: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => auth()->id()
            ]);

            $message = 'Gagal membuat pengajuan. ';
            
            // Provide a slightly more helpful message for common DB errors without raw SQL
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                $message .= 'Terjadi duplikasi nomor pengajuan, silakan coba lagi dalam beberapa saat.';
            } else {
                $message .= 'Terjadi kesalahan pada server.';
            }

            return response()->json(['message' => $message], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data = PengajuanRuangan::with([
                'status', 
                'user', 
                'dokumen_pendukung', 
                'items.ruangan.building',
                'histories' => function($q) {
                    $q->orderBy('sequence', 'asc');
                },
                'histories.user',
                'histories.status.role'
            ])->findOrFail($id);

            return response()->json([
                'result' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengambil detail pengajuan: ' . $e->getMessage()], 404);
        }
    }

    /**
     * Approve a pengajuan.
     */
    public function approve(Request $request)
    {
        try {
            $request->validate([
                'pengajuan_id' => 'required|uuid',
                'catatan' => 'nullable|string'
            ]);

            return DB::transaction(function () use ($request) {
                $user = auth()->user();
                $pengajuan = PengajuanRuangan::with(['status.role', 'user'])->findOrFail($request->pengajuan_id);
                $currentStatus = $pengajuan->status;

                if (!$currentStatus) {
                    return response()->json(['message' => 'Status pengajuan tidak ditemukan.'], 404);
                }

                // 1. Otorisasi Fleksibel
                // Pastikan role user yang login mencakup role yang ditugaskan untuk status ini
                $requiredRole = $currentStatus->role ? $currentStatus->role->name_role : null;
                $userRoles = $user->roles->pluck('name_role')->toArray();

                if (!$requiredRole || !in_array($requiredRole, $userRoles)) {
                    return response()->json(['message' => 'Anda tidak berwenang melakukan approve pada tahap ini.'], 403);
                }

                // 2. Tentukan status selanjutnya (Logic Spesifik & Dinamis)
                $nextStatus = null;
                
                // KASUS SPESIFIK: Tipe PEMBELAJARAN, Role TENAGA_TU, dan Status VERIFIKASI_TU
                if ($pengajuan->tipe_pengajuan === 'PEMBELAJARAN' && $currentStatus->nama_status === 'VERIFIKASI_TU' && $requiredRole === 'TENAGA_TU') {
                    // Lompat ke status final
                    $nextStatus = WorkflowStep::where('tipe_pengajuan', 'PEMBELAJARAN')
                        ->where(function($q) {
                            $q->where('nama_status', 'COMPLETED')
                              ->orWhere('nama_status', 'DISETUJUI')
                              ->orWhere('is_final', true);
                        })->first();
                } else {
                    // KASUS NORMAL: Ambil urutan langkah selanjutnya dari Workflow
                    $nextStatus = WorkflowStep::where('tipe_pengajuan', $pengajuan->tipe_pengajuan)
                        ->where('urutan', '>', $currentStatus->urutan)
                        ->orderBy('urutan', 'asc')
                        ->first();
                }

                if (!$nextStatus) {
                    return response()->json(['message' => 'Workflow selanjutnya tidak ditemukan atau pengajuan sudah final.'], 400);
                }

                // 3. Update Status Pengajuan
                $pengajuan->current_status_id = $nextStatus->id;
                $pengajuan->save();

                $isFinal = $nextStatus->is_final || in_array($nextStatus->nama_status, ['COMPLETED', 'DISETUJUI']);

                // 4. Catat Sejarah Approval (History)
                $lastSequence = PengajuanHistory::where('pengajuan_id', $pengajuan->id)->max('sequence') ?? 0;
                
                // Record action dari user yang meng-approve
                PengajuanHistory::create([
                    'pengajuan_id' => $pengajuan->id,
                    'status_id' => $nextStatus->id,
                    'user_id' => $user->id,
                    'aksi' => $isFinal ? 'COMPLETED' : 'APPROVE',
                    'catatan' => $request->catatan,
                    'sequence' => $lastSequence + 1,
                ]);

                // 5. Sistem Notifikasi
                if ($isFinal) {
                    // Kirim Notifikasi Selesai ke Pembuat Pengajuan
                    $requester = $pengajuan->user;
                    if ($requester) {
                        try {
                            $requester->notify(new \App\Notifications\PengajuanCompletedNotification($pengajuan));
                        } catch (\Exception $e) {
                            logger()->error('Notification Error (Completed): ' . $e->getMessage());
                        }
                    }
                } else {
                    // Kirim Notifikasi ke Approver Selanjutnya
                    if ($nextStatus->role) {
                        $nextApprovers = $nextStatus->role->users;
                        if ($nextApprovers && $nextApprovers->count() > 0) {
                            foreach ($nextApprovers as $approver) {
                                try {
                                    $approver->notify(new NewPengajuanNotification($pengajuan, $user));
                                } catch (\Exception $e) {
                                    logger()->error('Notification Error (Next Approver): ' . $e->getMessage());
                                }
                            }
                        }
                    }
                }

                return response()->json([
                    'message' => 'Pengajuan berhasil di-approve.',
                    'data' => $pengajuan
                ]);
            });
        } catch (\Exception $e) {
            logger()->error('Approve Error: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat memproses approval.'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
