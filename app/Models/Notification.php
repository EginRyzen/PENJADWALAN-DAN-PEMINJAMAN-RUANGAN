<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'notifications';

    protected $fillable = [
        'type',
        'notifiable_id',
        'notifiable_type',
        'data',
        'read_at',
    ];

    // Cast kolom data JSON menjadi array otomatis saat diakses di Vue
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Relasi Polimorfik: Mengambil model pemilik notifikasi (User/Dosen/Mahasiswa)
     *
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}