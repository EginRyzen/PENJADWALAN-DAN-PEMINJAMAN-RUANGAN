<?php

namespace App\Services;

use App\Models\JadwalUjian;
use App\Models\MasterDataKelas;
use App\Models\MasterDataMataKuliah;
use App\Models\MasterDataKelasMataKuliah;
use App\Models\MasterDataDosen;
use App\Models\MasterDataHariLibur;
use App\Models\MasterSksSetting;
use App\Models\MatserOperationalSchedule;
use App\Models\DataBaseBuildingRoom;
use Carbon\Carbon;

class CspGeneratorService
{
    /**
     * Generate jadwal ujian otomatis berdasarkan context yang diberikan.
     */
    public function generate(string $periodeId, string $tipe, string $startDate): array
    {
        // 1. Ambil semua kelas yang ada di periode ini
        $kelasList = MasterDataKelas::with(['programStudi'])
            ->withCount('mahasiswas')
            ->where('periode_id', $periodeId)
            ->get();

        // Map frontend "pembelajaran" ke database "pelajaran" untuk SKS setting
        $dbType = ($tipe === 'pembelajaran') ? 'pelajaran' : $tipe;

        // 2. Ambil setting SKS untuk tipe ujian ini
        $sksSetting = MasterSksSetting::where('type', $dbType)
            ->where('status', 'aktif')
            ->first();

        if (!$sksSetting) {
            throw new \Exception("SKS setting untuk tipe '{$tipe}' ({$dbType}) tidak ditemukan atau tidak aktif.");
        }

        // 3. Ambil jam operasional per hari
        $operasionalList = MatserOperationalSchedule::where('sks_setting_id', $sksSetting->id)
            ->where('status', 'aktif')
            ->get()
            ->keyBy('day'); // key: 'senin', 'selasa', ...

        // 4. Ambil hari libur (semua yang terdaftar)
        $hariLiburDates = MasterDataHariLibur::pluck('tanggal')
            ->map(fn($t) => Carbon::parse($t)->toDateString())
            ->toArray();

        // 5. Ambil ruangan yang boleh untuk ujian
        $ruanganList = DataBaseBuildingRoom::where('can_ujian', true)
            ->where('room_status', 'active')
            ->whereHas('building', function ($q) {
                $q->where('building_status', 'active');
            })
            ->get()
            ->shuffle(); // Acak agar tidak hanya ruangan yang sama yang dipakai

        // 6. Ambil semua dosen
        $dosenList = MasterDataDosen::where('status', 'Aktif')
            ->get()
            ->shuffle(); // Acak agar beban dosen merata

        // 7. Kumpulkan semua mata kuliah yang perlu dijadwalkan (Berdasarkan Plotting Kelas Mata Kuliah)
        $matkulPerKelas = [];
        $isUjian = in_array($tipe, ['uts', 'uas']);

        foreach ($kelasList as $kelas) {
            // Ambil plotting mata kuliah khusus untuk kelas ini dari table junction
            $plottingList = MasterDataKelasMataKuliah::with('mataKuliah')
                ->where('kelas_id', $kelas->id)
                ->get();

            foreach ($plottingList as $plotting) {
                $mk = $plotting->mataKuliah;
                if (!$mk) continue;

                // Gunakan sks_ujian jika tipe adalah UTS/UAS, jika tidak gunakan sks biasa
                $sksBasis = ($isUjian) ? ($mk->sks_ujian ?: 1) : $mk->sks;
                
                $matkulPerKelas[] = [
                    'kelas'    => $kelas,
                    'matkul'   => $mk,
                    'durasi'   => $sksBasis * $sksSetting->duration_minutes,
                ];
            }
        }

        // 8. Build jadwal dengan algoritma greedy + CSP constraint
        $result          = [];
        $busyRuangan     = []; // key: "tanggal|ruangan_id" => [[start, end], ...]
        $busyDosen       = []; // key: "tanggal|dosen_id"   => [[start, end], ...]
        $busyKelas       = []; // key: "tanggal|kelas_id"   => [[start, end], ...]
        $dosenDailyCount = []; // key: "tanggal|dosen_id"   => int (jumlah sesi per hari)
        $currentDate     = Carbon::parse($startDate);

        // Cari kapasitas ruangan terbesar
        $maxRoomCapacity = $ruanganList->max('room_capacity') ?? 0;

        foreach ($matkulPerKelas as $item) {
            $scheduled = false;
            $conflictReason = 'Tidak ada slot waktu & ruangan yang tersedia dalam 60 hari ke depan.';

            // Cek apakah jumlah peserta melebihi kapasitas ruangan terbesar
            $pesertaCount = $item['kelas']->mahasiswas_count ?? 0;
            if ($pesertaCount > $maxRoomCapacity) {
                $conflictReason = "Kapasitas ruangan tidak mencukupi. (Peserta: {$pesertaCount}, Max Ruangan: {$maxRoomCapacity})";
                // Skip pencarian karena pasti tidak akan muat
                $tryDate = $currentDate->copy();
                $maxTry = 0; 
            } else {
                $tryDate = $currentDate->copy();
                $maxTry  = 60; // batasi 60 hari ke depan
            }

            for ($d = 0; $d < $maxTry && !$scheduled; $d++) {
                // Skip hari libur & hari minggu
                if (in_array($tryDate->toDateString(), $hariLiburDates) || $tryDate->dayOfWeek === 0) {
                    $tryDate->addDay();
                    continue;
                }

                $hariKey = strtolower($tryDate->locale('id')->isoFormat('dddd'));

                // Pastikan ada operasional di hari ini
                if (!isset($operasionalList[$hariKey])) {
                    $tryDate->addDay();
                    continue;
                }

                $ops = $operasionalList[$hariKey];

                // Cari slot waktu kosong di hari ini
                $slot = $this->findSlot(
                    $tryDate->toDateString(),
                    $item['durasi'],
                    $ops,
                    $busyRuangan,
                    $busyDosen,
                    $busyKelas,
                    $dosenDailyCount,
                    $ruanganList,
                    $dosenList,
                    $item['kelas']
                );

                if ($slot) {
                    $dateKey = $tryDate->toDateString();
                    
                    // Catat interval sibuk
                    $busyRuangan["{$dateKey}|{$slot['ruangan_id']}"][] = [$slot['jam_mulai'], $slot['jam_selesai']];
                    $busyDosen["{$dateKey}|{$slot['dosen_id']}"][]     = [$slot['jam_mulai'], $slot['jam_selesai']];
                    $busyKelas["{$dateKey}|{$item['kelas']->id}"][]    = [$slot['jam_mulai'], $slot['jam_selesai']];
                    // Tambah counter harian dosen (max 2 sesi/hari)
                    $dosenDailyCount["{$dateKey}|{$slot['dosen_id']}"] =
                        ($dosenDailyCount["{$dateKey}|{$slot['dosen_id']}"] ?? 0) + 1;

                    $result[] = [
                        'id'               => 'TMP-' . uniqid(),
                        'periode_id'       => $periodeId,
                        'tipe'             => $tipe,
                        'mata_kuliah_id'   => $item['matkul']->id,
                        'mk_kode'          => $item['matkul']->kode,
                        'mk_nama'          => $item['matkul']->nama,
                        'sks'              => $item['matkul']->sks,
                        'kelas_id'         => $item['kelas']->id,
                        'kelas'            => $item['kelas']->nama_kelas,
                        'prodi_id'         => $item['kelas']->program_studi_id,
                        'prodi_kode'       => $item['kelas']->programStudi->kode ?? '-',
                        'prodi_nama'       => $item['kelas']->programStudi->nama ?? '-',
                        'tanggal'          => $tryDate->toDateString(),
                        'hari'             => ucfirst($hariKey),
                        'jam_mulai'        => $slot['jam_mulai'],
                        'jam_selesai'      => $slot['jam_selesai'],
                        'durasi'           => $item['durasi'],
                        'dosen_id'         => $slot['dosen_id'],
                        'dosen_nama'       => $slot['dosen_nama'],
                        'ruangan_id'       => $slot['ruangan_id'],
                        'ruangan_nama'     => $slot['ruangan_nama'],
                        'kapasitas'        => $slot['kapasitas'],
                        'jumlah_peserta'   => $pesertaCount,
                        'status'           => 'ok',
                        'conflict_reason'  => null,
                    ];

                    $scheduled = true;
                }

                $tryDate->addDay();
            }

            // Jika tidak bisa terjadwalkan, masukkan dengan status conflict
            if (!$scheduled) {
                $result[] = [
                    'id'               => 'TMP-' . uniqid(),
                    'periode_id'       => $periodeId,
                    'tipe'             => $tipe,
                    'mata_kuliah_id'   => $item['matkul']->id,
                    'mk_kode'          => $item['matkul']->kode,
                    'mk_nama'          => $item['matkul']->nama,
                    'sks'              => $item['matkul']->sks,
                    'kelas_id'         => $item['kelas']->id,
                    'kelas'            => $item['kelas']->nama_kelas,
                    'prodi_id'         => $item['kelas']->program_studi_id,
                    'prodi_kode'       => $item['kelas']->programStudi->kode ?? '-',
                    'prodi_nama'       => $item['kelas']->programStudi->nama ?? '-',
                    'tanggal'          => null,
                    'hari'             => null,
                    'jam_mulai'        => null,
                    'jam_selesai'      => null,
                    'durasi'           => $item['durasi'],
                    'dosen_id'         => null,
                    'dosen_nama'       => '-',
                    'ruangan_id'       => null,
                    'ruangan_nama'     => '-',
                    'kapasitas'        => 0,
                    'jumlah_peserta'   => $pesertaCount,
                    'status'           => 'conflict',
                    'conflict_reason'  => $conflictReason,
                ];
            }
        }

        return $result;
    }

    /**
     * Validasi jadwal yang sudah ada (biasanya setelah edit manual).
     * Mengecek bentrok & kepatuhan terhadap jam operasional/libur.
     */
    public function validate(array $items, string $periodeId, string $tipe, string $startDate): array
    {
        $dbType = ($tipe === 'pembelajaran') ? 'pelajaran' : $tipe;
        $sksSetting = MasterSksSetting::where('type', $dbType)->where('status', 'aktif')->first();
        if (!$sksSetting) throw new \Exception("SKS setting tidak aktif.");

        $operasionalList = MatserOperationalSchedule::where('sks_setting_id', $sksSetting->id)
            ->where('status', 'aktif')->get()->keyBy('day');

        // Ambil hari libur (semua yang terdaftar)
        $hariLiburDates = MasterDataHariLibur::pluck('tanggal')
            ->map(fn($t) => Carbon::parse($t)->toDateString())
            ->toArray();

        $busyRuangan = []; 
        $busyDosen    = []; 
        $busyKelas    = []; 
        $result       = [];

        // Sort items: prioritaskan yang sudah ada datanya
        foreach ($items as $item) {
            $isConflict = false;
            $reason     = null;

            // Normalisasi jam (pastikan format H:i)
            $jamMulai   = !empty($item['jam_mulai']) ? substr($item['jam_mulai'], 0, 5) : null;
            $jamSelesai = !empty($item['jam_selesai']) ? substr($item['jam_selesai'], 0, 5) : null;

            if (empty($item['tanggal']) || empty($jamMulai) || empty($item['ruangan_id'])) {
                $isConflict = true;
                $reason     = "Data waktu atau ruangan belum lengkap.";
            } else {
                $dateKey = $item['tanggal'];
                $hariKey = strtolower(Carbon::parse($dateKey)->locale('id')->isoFormat('dddd'));

                // 1. Cek Hari Libur / Minggu
                if (in_array($dateKey, $hariLiburDates) || Carbon::parse($dateKey)->dayOfWeek === 0) {
                    $isConflict = true;
                    $reason     = "Hari tersebut adalah hari libur atau hari Minggu.";
                }
                // 2. Cek Jam Operasional
                elseif (!isset($operasionalList[$hariKey])) {
                    $isConflict = true;
                    $reason     = "Tidak ada jam operasional di hari " . ucfirst($hariKey);
                } else {
                    $ops = $operasionalList[$hariKey];
                    $opsStart = substr($ops->start_time, 0, 5);
                    $opsEnd   = substr($ops->end_time, 0, 5);

                    if ($jamMulai < $opsStart || $jamSelesai > $opsEnd) {
                        $isConflict = true;
                        $reason     = "Diluar jam operasional ({$opsStart} - {$opsEnd})";
                    }
                }

                // 3. Cek Bentrok (Resource Overlap)
                if (!$isConflict) {
                    $slotKeyRuangan = "{$dateKey}|{$item['ruangan_id']}";
                    $slotKeyDosen   = !empty($item['dosen_id']) ? "{$dateKey}|{$item['dosen_id']}" : null;
                    $slotKeyKelas   = "{$dateKey}|{$item['kelas_id']}";

                    if ($this->hasOverlap($jamMulai, $jamSelesai, $busyRuangan[$slotKeyRuangan] ?? [])) {
                        $isConflict = true; $reason = "Bentrok Ruangan: Sudah digunakan di jam tersebut.";
                    } elseif ($slotKeyDosen && $this->hasOverlap($jamMulai, $jamSelesai, $busyDosen[$slotKeyDosen] ?? [])) {
                        $isConflict = true; $reason = "Bentrok Dosen: Dosen mengajar di tempat lain di jam tersebut.";
                    } elseif ($this->hasOverlap($jamMulai, $jamSelesai, $busyKelas[$slotKeyKelas] ?? [])) {
                        $isConflict = true; $reason = "Bentrok Kelas: Mahasiswa memiliki ujian lain di jam tersebut.";
                    }

                    if (!$isConflict) {
                        // Catat sebagai busy jika ok
                        $busyRuangan[$slotKeyRuangan][] = [$jamMulai, $jamSelesai];
                        if ($slotKeyDosen) $busyDosen[$slotKeyDosen][] = [$jamMulai, $jamSelesai];
                        $busyKelas[$slotKeyKelas][]     = [$jamMulai, $jamSelesai];
                    }
                }
            }

            $item['status']          = $isConflict ? 'conflict' : 'ok';
            $item['conflict_reason'] = $reason;
            $result[] = $item;
        }

        return $result;
    }

    /**
     * Cari slot waktu kosong: cek ruangan, dosen & kelas tidak bentrok (overlap).
     */
    private function findSlot(
        string $tanggal,
        int $durasi,
        $ops,
        array &$busyRuangan,
        array &$busyDosen,
        array &$busyKelas,
        array &$dosenDailyCount,
        $ruanganList,
        $dosenList,
        $kelas
    ): ?array {
        $startOps  = Carbon::createFromFormat('H:i:s', $ops->start_time);
        $endOps    = Carbon::createFromFormat('H:i:s', $ops->end_time);
        $breakStart= Carbon::createFromFormat('H:i:s', $ops->break_start);
        $breakEnd  = Carbon::createFromFormat('H:i:s', $ops->break_end);

        // Coba dari jam operasional mulai, step 30 menit
        $tryTime = $startOps->copy();

        while ($tryTime->copy()->addMinutes($durasi)->lte($endOps)) {
            $jamMulai   = $tryTime->format('H:i');
            $jamSelesai = $tryTime->copy()->addMinutes($durasi)->format('H:i');

            // Skip jika overlap dengan jam istirahat
            if ($this->isOverlap($jamMulai, $jamSelesai, $breakStart->format('H:i'), $breakEnd->format('H:i'))) {
                $tryTime = $breakEnd->copy();
                continue;
            }

            // 1. Cek apakah Kelas ini sudah ada ujian di jam tersebut (Bentrok Kelas)
            $kelasBusy = $busyKelas["{$tanggal}|{$kelas->id}"] ?? [];
            if ($this->hasOverlap($jamMulai, $jamSelesai, $kelasBusy)) {
                $tryTime->addMinutes(30);
                continue;
            }

            // 2. Cari ruangan yang kosong di jam tersebut
            $shuffledRooms = $ruanganList->shuffle();
            foreach ($shuffledRooms as $ruangan) {
                // Cek Kapasitas: Ruangan harus cukup untuk menampung peserta
                if ($ruangan->room_capacity < ($kelas->mahasiswas_count ?? 0)) continue;

                $ruBusy = $busyRuangan["{$tanggal}|{$ruangan->id}"] ?? [];
                if ($this->hasOverlap($jamMulai, $jamSelesai, $ruBusy)) continue;

                // 3. Cari dosen yang kosong di jam tersebut dan belum mengawasi >= 2 kali hari ini
                foreach ($dosenList as $dosen) {
                    // Batasi: 1 dosen maksimal 2 kali mengawasi per hari
                    $dosenCountToday = $dosenDailyCount["{$tanggal}|{$dosen->id}"] ?? 0;
                    if ($dosenCountToday >= 2) continue;

                    $doBusy = $busyDosen["{$tanggal}|{$dosen->id}"] ?? [];
                    if ($this->hasOverlap($jamMulai, $jamSelesai, $doBusy)) continue;

                    return [
                        'jam_mulai'    => $jamMulai,
                        'jam_selesai'  => $jamSelesai,
                        'ruangan_id'   => $ruangan->id,
                        'ruangan_nama' => $ruangan->room_name,
                        'kapasitas'    => $ruangan->room_capacity,
                        'dosen_id'     => $dosen->id,
                        'dosen_nama'   => $dosen->nama,
                    ];
                }
            }

            $tryTime->addMinutes(30);
        }

        return null;
    }

    /**
     * Cek apakah interval [s1, e1] overlap dengan list intervals [[s, e], ...]
     */
    private function hasOverlap(string $start, string $end, array $intervals): bool
    {
        foreach ($intervals as $inv) {
            if ($this->isOverlap($start, $end, $inv[0], $inv[1])) return true;
        }
        return false;
    }

    /**
     * Cek apakah dua interval waktu overlap
     */
    private function isOverlap(string $s1, string $e1, string $s2, string $e2): bool
    {
        return ($s1 < $e2 && $e1 > $s2);
    }
}
