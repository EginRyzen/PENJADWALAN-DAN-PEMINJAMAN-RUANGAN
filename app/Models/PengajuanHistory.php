<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanHistory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'pengajuan_id',
        'status_id',
        'user_id',
        'aksi',
        'catatan',
        'sequence',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanRuangan::class, 'pengajuan_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(WorkflowStep::class, 'status_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
