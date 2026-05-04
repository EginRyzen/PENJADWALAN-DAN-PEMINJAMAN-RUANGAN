<?php
// Kurangi dulu S1 Akuntansi 2024 & 2025 dari 20 ke 10
$akuntansiKelas = \App\Models\MasterDataKelas::whereHas('programStudi', function($q) {
    $q->where('nama', 'like', '%Akuntansi%');
})->get();

foreach ($akuntansiKelas as $kelas) {
    $keepIds = \App\Models\MasterDataMahasiswa::where('kelas_id', $kelas->id)
        ->orderBy('created_at')
        ->limit(10)
        ->pluck('id');
    
    $deleted = \App\Models\MasterDataMahasiswa::where('kelas_id', $kelas->id)
        ->whereNotIn('id', $keepIds)
        ->delete();
    echo "Trim {$kelas->nama_kelas} Akuntansi: hapus {$deleted}, sisa 10\n";
}

// Sekarang seed kelas yang masih 0 mahasiswanya dengan NIM unik berbasis timestamp + counter
$counter = (int)(microtime(true) * 1000);

$kelasList = \App\Models\MasterDataKelas::with(['programStudi'])->get();

foreach ($kelasList as $kelas) {
    $existing = \App\Models\MasterDataMahasiswa::where('kelas_id', $kelas->id)->count();
    $toAdd = 10 - $existing;

    $prodiNama = $kelas->programStudi ? $kelas->programStudi->nama : 'XX';

    if ($toAdd <= 0) {
        echo "  {$kelas->nama_kelas} ({$prodiNama}): skip (sudah {$existing})\n";
        continue;
    }

    for ($i = 1; $i <= $toAdd; $i++) {
        $counter++;
        $num = str_pad($existing + $i, 3, '0', STR_PAD_LEFT);

        \App\Models\MasterDataMahasiswa::create([
            'nim'              => 'MHS' . $counter,
            'nama'             => "Mahasiswa {$kelas->nama_kelas} {$num}",
            'program_studi_id' => $kelas->program_studi_id,
            'kelas_id'         => $kelas->id,
            'semester'         => 2,
            'periode_id'       => $kelas->periode_id,
            'status'           => 'Aktif',
        ]);
    }
    echo "  {$kelas->nama_kelas} ({$prodiNama}): +{$toAdd} mahasiswa\n";
}

echo "\n=== VERIFIKASI AKHIR ===\n";
$total = \App\Models\MasterDataMahasiswa::count();
$klsList = \App\Models\MasterDataKelas::withCount('mahasiswas')->having('mahasiswas_count', '>', 0)->get();
foreach ($klsList as $k) {
    echo "  {$k->nama_kelas}: {$k->mahasiswas_count} mhs\n";
}
echo "Total seluruh mahasiswa: {$total}\n";
