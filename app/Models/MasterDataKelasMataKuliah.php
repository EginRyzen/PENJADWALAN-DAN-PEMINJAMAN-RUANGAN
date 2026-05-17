<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MasterDataKelasMataKuliah extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'master_data_kelas_mata_kuliahs';

    protected $fillable = [
        'kelas_id',
        'mata_kuliah_id',
        'semester',
    ];

    // ─── Relasi ───────────────────────────────────────────────────────────────

    public function kelas()
    {
        return $this->belongsTo(MasterDataKelas::class, 'kelas_id');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MasterDataMataKuliah::class, 'mata_kuliah_id');
    }

    // ─── Static: Insert ───────────────────────────────────────────────────────

    /**
     * Insert satu record penggabungan kelas & mata kuliah.
     * Idempotent: lewati jika sudah ada kombinasi yang sama.
     *
     * @param  string  $kelasId
     * @param  string  $mataKuliahId
     * @param  int     $semester
     * @return static|null  Record baru, atau null jika sudah ada.
     */
    public static function insertKelasMataKuliah(string $kelasId, string $mataKuliahId, int $semester): ?static
    {
        $exists = static::where('kelas_id', $kelasId)
            ->where('mata_kuliah_id', $mataKuliahId)
            ->where('semester', $semester)
            ->exists();

        if ($exists) {
            return null;
        }

        return static::create([
            'kelas_id'       => $kelasId,
            'mata_kuliah_id' => $mataKuliahId,
            'semester'       => $semester,
        ]);
    }

    /**
     * Bulk insert penggabungan semua kelas angkatan 2025 dengan semua
     * mata kuliah semester 3, dicocokkan berdasarkan program_studi_id.
     *
     * @return array{inserted: int, skipped: int}
     */
    public static function insertKelas2025DenganSemester3(): array
    {
        // Ambil kelas angkatan 2025
        $kelasAngkatan2025 = MasterDataKelas::where('nama_kelas', 'like', '%2025%')->get();

        // Ambil matkul semester 3, group by program_studi_id
        $mataKuliahByProdi = MasterDataMataKuliah::where('semester', 3)
            ->get()
            ->groupBy('program_studi_id');

        $inserted = 0;
        $skipped  = 0;

        foreach ($kelasAngkatan2025 as $kelas) {
            $mataKuliahProdi = $mataKuliahByProdi[$kelas->program_studi_id] ?? collect();

            foreach ($mataKuliahProdi as $mataKuliah) {
                $result = static::insertKelasMataKuliah($kelas->id, $mataKuliah->id, 3);
                $result ? $inserted++ : $skipped++;
            }
        }

        return ['inserted' => $inserted, 'skipped' => $skipped];
    }

    // ─── Static: Get ──────────────────────────────────────────────────────────

    /**
     * Ambil semua penggabungan kelas+matkul berdasarkan kelas_id dan semester.
     * Sertakan relasi mataKuliah agar langsung bisa diakses.
     *
     * @param  string  $kelasId
     * @param  int     $semester
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByKelasAndSemester(string $kelasId, int $semester)
    {
        return static::with('mataKuliah')
            ->where('kelas_id', $kelasId)
            ->where('semester', $semester)
            ->get();
    }

    /**
     * Ambil semua penggabungan kelas 2025 + matkul semester 3.
     * Sertakan relasi kelas (beserta periode & prodi) dan mataKuliah.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getKelas2025Semester3()
    {
        return static::with(['kelas.periode', 'kelas.programStudi', 'mataKuliah'])
            ->where('semester', 3)
            ->whereHas('kelas', function ($q) {
                $q->where('nama_kelas', 'like', '%2025%');
            })
            ->get();
    }
}
