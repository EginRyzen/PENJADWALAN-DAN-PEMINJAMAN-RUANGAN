<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MasterDataKelasMataKuliah;

echo "=== VERIFIKASI HASIL INSERT ===\n";
echo "Total semester 3: " . MasterDataKelasMataKuliah::where('semester', 3)->count() . "\n";

echo "\n=== TEST getKelas2025Semester3() ===\n";
$data = MasterDataKelasMataKuliah::getKelas2025Semester3();
echo "Total data kelas 2025 + matkul semester 3: " . $data->count() . "\n";

// Tampilkan 5 sample
$sample = $data->take(5);
foreach ($sample as $item) {
    $namaKelas = $item->kelas->nama_kelas ?? 'N/A';
    $namaMk    = $item->mataKuliah->nama ?? 'N/A';
    $kodeMk    = $item->mataKuliah->kode ?? 'N/A';
    echo "  Kelas: {$namaKelas} | MK: [{$kodeMk}] {$namaMk} | Semester: {$item->semester}\n";
}

echo "\n=== TEST getByKelasAndSemester() ===\n";
// Ambil satu kelas 2025 sebagai sample
$kelasContoh = \App\Models\MasterDataKelas::where('nama_kelas', 'like', '%2025%')->first();
if ($kelasContoh) {
    echo "Kelas: " . $kelasContoh->nama_kelas . " (ID: " . $kelasContoh->id . ")\n";
    $matakuliahs = MasterDataKelasMataKuliah::getByKelasAndSemester($kelasContoh->id, 3);
    echo "Mata kuliah semester 3 di kelas ini: " . $matakuliahs->count() . " matkul\n";
    foreach ($matakuliahs as $mk) {
        echo "  - [{$mk->mataKuliah->kode}] {$mk->mataKuliah->nama}\n";
    }
}

echo "\n=== TEST insertKelasMataKuliah() (duplikat -> skip) ===\n";
$first = MasterDataKelasMataKuliah::where('semester', 3)->first();
$result = MasterDataKelasMataKuliah::insertKelasMataKuliah($first->kelas_id, $first->mata_kuliah_id, 3);
echo "Insert duplikat: " . ($result === null ? "SKIP (sudah ada) ✓" : "INSERT BARU (seharusnya skip!)") . "\n";
