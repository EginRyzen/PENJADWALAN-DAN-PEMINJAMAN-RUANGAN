<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MasterDataMataKuliah;

$mk = MasterDataMataKuliah::all();
echo "Total Mata Kuliah: " . $mk->count() . "\n";

echo "\n--- Distribusi SKS ---\n";
foreach ($mk->groupBy('sks') as $sks => $items) {
    echo "SKS $sks: " . $items->count() . " mata kuliah\n";
}

echo "\n--- Distribusi SKS Ujian (Sekarang) ---\n";
foreach ($mk->groupBy('sks_ujian') as $sksUjian => $items) {
    echo "SKS Ujian $sksUjian: " . $items->count() . " mata kuliah\n";
}

echo "\n--- Melakukan Update SKS Ujian ---\n";
// Logika: 
// Jika SKS >= 3, set sks_ujian = 3
// Jika SKS < 3, set sks_ujian = 2
// Ini akan membuat variasi 2 dan 3 sesuai permintaan user.

$countUpdated = 0;
foreach ($mk as $item) {
    $newSksUjian = ($item->sks >= 3) ? 3 : 2;
    $item->sks_ujian = $newSksUjian;
    $item->save();
    $countUpdated++;
}

echo "Berhasil update $countUpdated mata kuliah.\n";

echo "\n--- Distribusi SKS Ujian (Setelah Update) ---\n";
$mkNew = MasterDataMataKuliah::all();
foreach ($mkNew->groupBy('sks_ujian') as $sksUjian => $items) {
    echo "SKS Ujian $sksUjian: " . $items->count() . " mata kuliah\n";
}
