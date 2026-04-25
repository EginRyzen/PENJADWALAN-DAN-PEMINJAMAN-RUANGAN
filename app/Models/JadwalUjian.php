<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalUjian extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'periode_id',
        'tipe',
        'mata_kuliah_id',
        'kelas_id',
        'dosen_id',
        'ruangan_id',
        'tanggal',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'durasi_menit',
        'status_data',
        'status_konflik',
        'conflict_reason',
        'generated_by',
        'saved_by',
        'saved_at',
        'notified_at',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'saved_at'   => 'datetime',
        'notified_at'=> 'datetime',
    ];

    // ===================== RELATIONS =====================

    public function periode()
    {
        return $this->belongsTo(MasterDataPeriode::class, 'periode_id');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MasterDataMataKuliah::class, 'mata_kuliah_id');
    }

    public function kelas()
    {
        return $this->belongsTo(MasterDataKelas::class, 'kelas_id');
    }

    public function dosen()
    {
        return $this->belongsTo(MasterDataDosen::class, 'dosen_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(DataBaseBuildingRoom::class, 'ruangan_id');
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function savedBy()
    {
        return $this->belongsTo(User::class, 'saved_by');
    }

    // ===================== SCOPES =====================

    /**
     * Scope: filter draft untuk periode + tipe tertentu
     */
    public function scopeDraftFor($query, string $periodeId, string $tipe)
    {
        return $query->where('periode_id', $periodeId)
                     ->where('tipe', $tipe)
                     ->where('status_data', 'draft');
    }

    /**
     * Scope: filter permanen untuk periode + tipe tertentu
     */
    public function scopePermanenFor($query, string $periodeId, string $tipe)
    {
        return $query->where('periode_id', $periodeId)
                     ->where('tipe', $tipe)
                     ->where('status_data', 'permanen');
    }
}
