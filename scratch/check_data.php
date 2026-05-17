<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\MasterDataKelas;
use App\Models\MasterDataMataKuliah;
use App\Models\MasterDataKelasMataKuliah;
use App\Models\MasterDataPeriode;

echo "=== SEMUA PERIODE ===\n";
$periodes = MasterDataPeriode::all();
foreach ($periodes as $p) {
    echo "ID: " . $p->id . " | Data: " . json_encode($p->toArray()) . "\n";
}

echo "\n=== SEMUA KELAS dengan Periode ===\n";
$kelas = MasterDataKelas::with('periode')->get();
foreach ($kelas as $k) {
    $tahun = $k->periode ? ($k->periode->tahun_akademik ?? $k->periode->tahun ?? $k->periode->nama ?? 'unknown') : 'no-periode';
    echo "ID: " . $k->id . " | Nama: " . $k->nama_kelas . " | prodi: " . $k->program_studi_id . " | tahun/periode: " . $tahun . "\n";
}
