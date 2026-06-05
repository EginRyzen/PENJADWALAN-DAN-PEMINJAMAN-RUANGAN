<?php

namespace App\Http\Controllers\Penjadwalan;

use App\Http\Controllers\Controller;
use App\Models\JadwalUjian;
use App\Models\MasterDataDosen;
use App\Models\User;
use App\Notifications\JadwalUjianPermanenNotification;
use App\Services\CspGeneratorService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\JadwalUjianExport;

class JadwalUjianController extends Controller
{
    use ApiResponse;

    protected CspGeneratorService $cspService;

    public function __construct(CspGeneratorService $cspService)
    {
        $this->cspService = $cspService;
    }

    // ─────────────────────────────────────────────────────────────────
    // GET /jadwal/draft?periode_id=&tipe=
    // Cek & ambil draft yang ada untuk periode + tipe ini
    // ─────────────────────────────────────────────────────────────────
    public function getDraft(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|uuid|exists:master_data_periodes,id',
            'tipe'       => 'required|in:uts,uas,pembelajaran',
        ]);

        try {
            // Cek semua jadwal untuk periode + tipe ini (bisa draft atau permanen)
            $jadwal = JadwalUjian::with(['mataKuliah', 'kelas' => function ($query) {
                $query->with('programStudi')->withCount('mahasiswas');
            }, 'dosen', 'ruangan'])
                ->where('periode_id', $request->periode_id)
                ->where('tipe', $request->tipe)
                ->orderBy('tanggal')
                ->get();

            if ($jadwal->isEmpty()) {
                return $this->successResponse(null, 'Tidak ada jadwal (draft/permanen) untuk periode dan tipe ini');
            }

            $isPermanen = $jadwal->every(fn($j) => $j->status_data === 'permanen');
            // Jika ada campuran (seharusnya tidak ada), kita anggap draft jika ada minimal 1 draft
            $statusUtama = $jadwal->contains('status_data', 'draft') ? 'draft' : 'permanen';

            $mapped = $jadwal->map(fn($j) => $this->mapJadwalRow($j, $j->status_data));

            return $this->successResponse([
                'exists'      => true,
                'is_permanen' => $statusUtama === 'permanen',
                'status'      => $statusUtama,
                'count'       => $jadwal->count(),
                'saved_at'    => $jadwal->max('updated_at'),
                'start_date'  => $jadwal->min('tanggal')?->toDateString(),
                'items'       => $mapped,
            ], 'Jadwal ditemukan');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // POST /jadwal/generate
    // Generate jadwal otomatis via CSP engine (tidak disimpan ke DB)
    // ─────────────────────────────────────────────────────────────────
    public function generate(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|uuid|exists:master_data_periodes,id',
            'tipe'       => 'required|in:uts,uas,pembelajaran',
            'start_date' => 'required|date_format:Y-m-d',
            'jadwal'     => 'nullable|array',
        ]);

        try {
            // Jika ada payload jadwal, berarti kita validasi bukan generate baru
            if ($request->has('jadwal')) {
                $validatedJadwal = $this->cspService->validate(
                    $request->jadwal,
                    $request->periode_id,
                    $request->tipe,
                    $request->start_date
                );
                return $this->successResponse($validatedJadwal, 'Jadwal berhasil divalidasi');
            }

            $jadwal = $this->cspService->generate(
                $request->periode_id,
                $request->tipe,
                $request->start_date
            );

            return $this->successResponse($jadwal, 'Jadwal berhasil digenerate');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // POST /jadwal/draft
    // Simpan / replace draft untuk periode + tipe ini
    // ─────────────────────────────────────────────────────────────────
    public function saveDraft(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|uuid|exists:master_data_periodes,id',
            'tipe'       => 'required|in:uts,uas,pembelajaran',
            'jadwal'     => 'required|array|min:1',
        ]);

        // Pastikan belum ada jadwal PERMANEN untuk periode + tipe ini
        $adaPermanen = JadwalUjian::permanenFor($request->periode_id, $request->tipe)->exists();
        if ($adaPermanen) {
            return $this->errorResponse(
                'Jadwal untuk periode dan tipe ini sudah disimpan permanen dan tidak dapat diubah.',
                422,
                'Unprocessable Entity'
            );
        }

        DB::beginTransaction();
        try {
            // Hapus draft lama untuk periode + tipe ini
            JadwalUjian::draftFor($request->periode_id, $request->tipe)->delete();

            $userId = $request->user()->id;

            foreach ($request->jadwal as $row) {
                JadwalUjian::create([
                    'periode_id'      => $request->periode_id,
                    'tipe'            => $request->tipe,
                    'mata_kuliah_id'  => $row['mata_kuliah_id'],
                    'kelas_id'        => $row['kelas_id'],
                    'dosen_id'        => $row['dosen_id'] ?? null,
                    'ruangan_id'      => $row['ruangan_id'] ?? null,
                    'tanggal'         => $row['tanggal'],
                    'hari'            => strtolower($row['hari']),
                    'jam_mulai'       => $row['jam_mulai'],
                    'jam_selesai'     => $row['jam_selesai'],
                    'durasi_menit'    => $row['durasi'],
                    'status_data'     => 'draft',
                    'status_konflik'  => $row['status'],
                    'conflict_reason' => $row['conflict_reason'] ?? null,
                    'generated_by'    => $userId,
                ]);
            }

            DB::commit();

            return $this->successResponse([
                'saved_at' => now(),
                'count'    => count($request->jadwal),
            ], 'Draft jadwal berhasil disimpan', 201, 'Created');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // PATCH /jadwal/draft/{id}
    // Update 1 baris jadwal (dari edit modal)
    // ─────────────────────────────────────────────────────────────────
    public function updateRow(Request $request, string $id)
    {
        $request->validate([
            'dosen_id'       => 'nullable|uuid|exists:master_data_dosens,id',
            'ruangan_id'     => 'nullable|uuid|exists:data_base_building_rooms,id',
            'tanggal'        => 'nullable|date_format:Y-m-d',
            'jam_mulai'      => 'nullable|date_format:H:i',
            'jam_selesai'    => 'nullable|date_format:H:i',
            'status_konflik' => 'nullable|in:ok,conflict,edited',
            'conflict_reason'=> 'nullable|string',
        ]);

        try {
            $jadwal = JadwalUjian::find($id);

            if (!$jadwal) {
                return $this->errorResponse('Jadwal tidak ditemukan', 404, 'Not Found');
            }

            if ($jadwal->status_data === 'permanen') {
                return $this->errorResponse('Jadwal permanen tidak dapat diubah', 422, 'Unprocessable Entity');
            }

            $jadwal->update([
                'dosen_id'        => $request->dosen_id ?? $jadwal->dosen_id,
                'ruangan_id'      => $request->ruangan_id ?? $jadwal->ruangan_id,
                'tanggal'         => $request->tanggal ?? $jadwal->tanggal,
                'jam_mulai'       => $request->jam_mulai ?? $jadwal->jam_mulai,
                'jam_selesai'     => $request->jam_selesai ?? $jadwal->jam_selesai,
                'status_konflik'  => $request->status_konflik ?? 'edited',
                'conflict_reason' => $request->conflict_reason,
            ]);

            return $this->successResponse($jadwal->fresh(), 'Jadwal berhasil diperbarui');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // PATCH /jadwal/permanen
    // Finalisasi draft menjadi permanen + kirim notifikasi email
    // ─────────────────────────────────────────────────────────────────
    public function savePermanen(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|uuid|exists:master_data_periodes,id',
            'tipe'       => 'required|in:uts,uas,pembelajaran',
            'jadwal'     => 'nullable|array', // Opsional: jika dikirim langsung dari hasil generate
        ]);

        // Jika data dikirim langsung, kita cek konflik dulu di payload (server-side validation)
        // Namun biasanya frontend sudah memvalidasi ini.

        DB::beginTransaction();
        try {
            $userId = $request->user()->id;

            if ($request->has('jadwal') && !empty($request->jadwal)) {
                // Hapus draft lama jika ada
                JadwalUjian::draftFor($request->periode_id, $request->tipe)->delete();

                // Simpan langsung sebagai permanen
                foreach ($request->jadwal as $row) {
                    JadwalUjian::create([
                        'periode_id'      => $request->periode_id,
                        'tipe'            => $request->tipe,
                        'mata_kuliah_id'  => $row['mata_kuliah_id'],
                        'kelas_id'        => $row['kelas_id'],
                        'dosen_id'        => $row['dosen_id'],
                        'ruangan_id'      => $row['ruangan_id'],
                        'tanggal'         => $row['tanggal'],
                        'hari'            => $row['hari'],
                        'jam_mulai'       => $row['jam_mulai'],
                        'jam_selesai'     => $row['jam_selesai'],
                        'durasi_menit'    => $row['durasi'],
                        'status_data'     => 'permanen',
                        'status_konflik'  => 'ok',
                        'generated_by'    => $userId,
                        'saved_by'        => $userId,
                        'saved_at'        => now(),
                    ]);
                }
            } else {
                // Perilaku lama: upgrade draft yang sudah ada di DB menjadi permanen
                $adaKonflik = JadwalUjian::draftFor($request->periode_id, $request->tipe)
                    ->where('status_konflik', 'conflict')
                    ->exists();

                if ($adaKonflik) {
                    return $this->errorResponse(
                        'Masih terdapat jadwal yang konflik. Selesaikan semua konflik sebelum menyimpan permanen.',
                        422,
                        'Unprocessable Entity'
                    );
                }

                $updated = JadwalUjian::draftFor($request->periode_id, $request->tipe)
                    ->update([
                        'status_data' => 'permanen',
                        'saved_by'    => $userId,
                        'saved_at'    => now(),
                    ]);
                
                if ($updated === 0) {
                     return $this->errorResponse('Tidak ada draft jadwal untuk disimpan permanen.', 404, 'Not Found');
                }
            }

            DB::commit();

            // Kirim notifikasi email ke dosen
            $this->kirimNotifikasiDosen($request->periode_id, $request->tipe);

            $count = JadwalUjian::permanenFor($request->periode_id, $request->tipe)->count();

            return $this->successResponse([
                'count'    => $count,
                'saved_at' => now(),
            ], 'Jadwal berhasil disimpan permanen');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // DELETE /jadwal/draft?periode_id=&tipe=
    // Hapus draft (saat reset / generate ulang)
    // ─────────────────────────────────────────────────────────────────
    public function deleteDraft(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|uuid|exists:master_data_periodes,id',
            'tipe'       => 'required|in:uts,uas,pembelajaran',
        ]);

        try {
            $deleted = JadwalUjian::draftFor($request->periode_id, $request->tipe)->delete();

            return $this->successResponse(
                ['deleted_count' => $deleted],
                'Draft jadwal berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // GET /jadwal?periode_id=&tipe=&status=
    // List jadwal (untuk ditampilkan, bisa draft atau permanen)
    // ─────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|uuid|exists:master_data_periodes,id',
            'tipe'       => 'required|in:uts,uas,pembelajaran',
            'status'     => 'nullable|in:draft,permanen',
        ]);

        try {
            $query = JadwalUjian::with(['mataKuliah', 'kelas' => function ($q) {
                $q->with('programStudi')->withCount('mahasiswas');
            }, 'dosen', 'ruangan'])
                ->where('periode_id', $request->periode_id)
                ->where('tipe', $request->tipe);

            if ($request->status) {
                $query->where('status_data', $request->status);
            }

            $jadwal = $query->orderBy('tanggal')->orderBy('jam_mulai')->get();
            $mapped = $jadwal->map(fn($j) => $this->mapJadwalRow($j, $j->status_data));

            return $this->successResponse($mapped, 'Daftar jadwal berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // GET /jadwal/export?periode_id=&tipe=&status=
    // Export jadwal ujian ke Excel
    // ─────────────────────────────────────────────────────────────────
    public function export(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|uuid|exists:master_data_periodes,id',
            'tipe'       => 'required|in:uts,uas,pembelajaran',
            'status'     => 'nullable|in:draft,permanen',
        ]);

        return Excel::download(new JadwalUjianExport($request->all()), 'jadwal_ujian.xlsx');
    }

    // ─────────────────────────────────────────────────────────────────
    // PRIVATE: Map model ke format frontend
    // ─────────────────────────────────────────────────────────────────
    private function mapJadwalRow(JadwalUjian $j, string $statusData): array
    {
        return [
            'id'             => $j->id,
            'mk_kode'        => $j->mataKuliah->kode ?? '-',
            'mk_nama'        => $j->mataKuliah->nama ?? '-',
            'sks'            => $j->mataKuliah->sks ?? 0,
            'mata_kuliah_id' => $j->mata_kuliah_id,
            'kelas_id'       => $j->kelas_id,
            'kelas'          => $j->kelas->nama_kelas ?? '-',
            'prodi_kode'     => $j->kelas->programStudi->kode ?? '-',
            'prodi_nama'     => $j->kelas->programStudi->nama ?? '-',
            'prodi_id'       => $j->kelas->program_studi_id ?? null,
            'tanggal'        => $j->tanggal ? $j->tanggal->toDateString() : null,
            'hari'           => $j->hari ? ucfirst($j->hari) : null,
            'jam_mulai'      => $j->jam_mulai,
            'jam_selesai'    => $j->jam_selesai,
            'durasi'         => $j->durasi_menit,
            'dosen_id'       => $j->dosen_id,
            'dosen_nama'     => $j->dosen->nama ?? '-',
            'ruangan_id'     => $j->ruangan_id,
            'ruangan_nama'   => $j->ruangan->room_name ?? '-',
            'kapasitas'      => $j->ruangan->room_capacity ?? 0,
            'jumlah_peserta' => $j->kelas->mahasiswas_count ?? 0,
            'status'         => $j->status_konflik,
            'status_data'    => $statusData,
            'conflict_reason'=> $j->conflict_reason,
        ];
    }

    // ─────────────────────────────────────────────────────────────────
    // PRIVATE: Kirim notifikasi email ke dosen (via queue)
    // ─────────────────────────────────────────────────────────────────
    private function kirimNotifikasiDosen(string $periodeId, string $tipe): void
    {
        $dosenIds = JadwalUjian::permanenFor($periodeId, $tipe)
            ->whereNotNull('dosen_id')
            ->distinct()
            ->pluck('dosen_id');

        $dosens = MasterDataDosen::whereIn('id', $dosenIds)->get();

        foreach ($dosens as $dosen) {
            // Ambil user berdasarkan identity_number = nip dosen
            $user = User::where('identity_number', $dosen->nip)->first();

            if (!$user || !$user->email) continue;

            // Ambil jadwal dosen ini
            $jadwalDosen = JadwalUjian::with(['mataKuliah', 'kelas', 'ruangan'])
                ->permanenFor($periodeId, $tipe)
                ->where('dosen_id', $dosen->id)
                ->orderBy('tanggal')
                ->get();

            // Tandai waktu notifikasi
            JadwalUjian::permanenFor($periodeId, $tipe)
                ->where('dosen_id', $dosen->id)
                ->update(['notified_at' => now()]);

            // Kirim notifikasi (queued)
            $user->notify(new JadwalUjianPermanenNotification($dosen, $jadwalDosen, $tipe));
        }
    }
}
