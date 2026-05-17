<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterDataKelas;
use App\Models\MasterDataMataKuliah;
use App\Models\MasterDataKelasMataKuliah;

class KelasMataKuliahSemester3Seeder extends Seeder
{
    /**
     * Seed penggabungan kelas angkatan 2025 dengan mata kuliah semester 3.
     *
     * Logika:
     * - Ambil semua kelas yang memiliki nama mengandung "2025" (angkatan 2025)
     * - Ambil semua mata kuliah dengan semester = 3
     * - Gabungkan kelas & matakuliah berdasarkan program_studi_id yang sama
     * - Insert ke master_data_kelas_mata_kuliahs dengan semester = 3
     * - Skip jika data sudah ada (idempotent)
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding penggabungan Kelas 2025 + Mata Kuliah Semester 3...');

        // Ambil kelas angkatan 2025 (nama kelas mengandung "2025")
        $kelasAngkatan2025 = MasterDataKelas::where('nama_kelas', 'like', '%2025%')
            ->get();

        $this->command->info("Ditemukan {$kelasAngkatan2025->count()} kelas angkatan 2025.");

        // Ambil semua mata kuliah semester 3, diindex by program_studi_id
        $mataKuliahSemester3 = MasterDataMataKuliah::where('semester', 3)->get();
        $this->command->info("Ditemukan {$mataKuliahSemester3->count()} mata kuliah semester 3.");

        // Group mata kuliah by program_studi_id untuk pencarian cepat
        $mataKuliahByProdi = $mataKuliahSemester3->groupBy('program_studi_id');

        $insertCount = 0;
        $skipCount   = 0;

        foreach ($kelasAngkatan2025 as $kelas) {
            $prodiId = $kelas->program_studi_id;

            // Cari mata kuliah semester 3 yang sesuai dengan prodi kelas ini
            if (!isset($mataKuliahByProdi[$prodiId])) {
                $this->command->warn(
                    "  Kelas '{$kelas->nama_kelas}' (prodi: {$prodiId}) tidak memiliki mata kuliah semester 3."
                );
                continue;
            }

            $mataKuliahProdi = $mataKuliahByProdi[$prodiId];

            foreach ($mataKuliahProdi as $mataKuliah) {
                // Cek apakah sudah ada agar tidak duplikat (idempotent)
                $exists = MasterDataKelasMataKuliah::where('kelas_id', $kelas->id)
                    ->where('mata_kuliah_id', $mataKuliah->id)
                    ->where('semester', 3)
                    ->exists();

                if ($exists) {
                    $skipCount++;
                    continue;
                }

                MasterDataKelasMataKuliah::create([
                    'kelas_id'      => $kelas->id,
                    'mata_kuliah_id' => $mataKuliah->id,
                    'semester'      => 3,
                ]);

                $insertCount++;
            }
        }

        $this->command->info("Seeding selesai! Insert: {$insertCount} | Skip (sudah ada): {$skipCount}");
    }
}
