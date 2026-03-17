<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowStep extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'nama_status',
        'tipe_pengajuan',
        'role_id',
        'urutan',
        'is_final',
    ];

    protected $casts = [
        'is_final' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function pengajuanRuangan()
    {
        return $this->hasMany(PengajuanRuangan::class, 'current_status_id');
    }
    public function histories()
    {
        return $this->hasMany(PengajuanHistory::class, 'status_id');
    }
}
