<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengajuanRuangan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'no_pengajuan',
        'tipe_pengajuan',
        'current_status_id',
        'ruangan_id',
        'user_id',
        'tanggal_pengajuan',
        'tanggal_start_peminjaman',
        'tanggal_end_peminjaman',
        'jam_mulai',
        'jam_selesai',
        'alasan',
        'dokumen_pendukung_id',
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'current_status_id');
    }

    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(DataBaseBuildingRoom::class, 'ruangan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dokumen_pendukung(): BelongsTo
    {
        return $this->belongsTo(DataDocument::class, 'dokumen_pendukung_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(PengajuanHistory::class, 'pengajuan_id');
    }
}
