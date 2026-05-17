<?php

use App\Models\MasterDataKelas;
use App\Models\MasterDataMahasiswa;
use App\Models\MasterDataKelasMataKuliah;
use Illuminate\Support\Str;

echo "============================================================\n";
echo " EKSEKUSI DATA SETUP (KELAS 2025 & MAHASISWA)\n";
echo "============================================================\n\n";

// 1. Eksekusi Penggabungan Kelas & MK (Logika sebelumnya)
echo "--- 1. Proses Plotting MK Semester 3 ---\n";
$hasilInsertMK = MasterDataKelasMataKuliah::insertKelas2025DenganSemester3();
echo "Status: Selesai\n";
echo "Berhasil Insert Baru : {$hasilInsertMK['inserted']}\n";
echo "Dilewati (Sudah Ada) : {$hasilInsertMK['skipped']}\n\n";

// 2. Eksekusi Insert Mahasiswa (30 per kelas)
echo "--- 2. Proses Insert Mahasiswa (30 per kelas) ---\n";

$allKelas = MasterDataKelas::all();
$totalKelas = $allKelas->count();
$totalMahasiswaInserted = 0;
$totalMahasiswaSkipped = 0;

foreach ($allKelas as $index => $kelas) {
    $currentCount = MasterDataMahasiswa::where('kelas_id', $kelas->id)->count();
    $needed = 30 - $currentCount;
    
    if ($needed <= 0) {
        $totalMahasiswaSkipped += 30;
        continue;
    }

    for ($i = 0; $i < $needed; $i++) {
        // Generate NIM unik: Gabungan Tahun + Kode Prodi (ambil 4 digit uuid) + Index Kelas + Counter
        $randomPart = substr($kelas->id, 0, 4);
        $nim = "2025" . strtoupper($randomPart) . str_pad($index, 3, '0', STR_PAD_LEFT) . str_pad($i + $currentCount, 3, '0', STR_PAD_LEFT);
        
        // Cek lagi NIM agar tidak bentrok
        if (MasterDataMahasiswa::where('nim', $nim)->exists()) {
            $nim .= rand(10, 99);
        }

        MasterDataMahasiswa::create([
            'nim'              => $nim,
            'nama'             => "Mahasiswa " . Str::random(5) . " (" . $kelas->nama_kelas . ")",
            'program_studi_id' => $kelas->program_studi_id,
            'kelas_id'         => $kelas->id,
            'semester'         => 3, // Default ke semester 3 sesuai request sebelumnya
            'periode_id'       => $kelas->periode_id,
            'status'           => 'Aktif',
        ]);
        $totalMahasiswaInserted++;
    }
    
    // Progress indicator setiap 10 kelas
    if (($index + 1) % 10 === 0 || ($index + 1) === $totalKelas) {
        echo "Progress: " . ($index + 1) . "/{$totalKelas} kelas diproses...\n";
    }
}

echo "\nStatus: Selesai\n";
echo "Total Mahasiswa Baru Di-insert : {$totalMahasiswaInserted}\n";
echo "Total Mahasiswa Sudah Ada      : {$totalMahasiswaSkipped}\n\n";

// 3. Verifikasi Tampilan
echo "--- 3. Verifikasi Data Terkini ---\n";
$summary = MasterDataKelas::withCount('mahasiswas')->take(5)->get();
foreach ($summary as $s) {
    echo "Kelas: {$s->nama_kelas} | Jumlah Mahasiswa: {$s->mahasiswas_count}\n";
}

echo "\n============================================================\n";
echo " SELESAI\n";
echo "============================================================\n";
