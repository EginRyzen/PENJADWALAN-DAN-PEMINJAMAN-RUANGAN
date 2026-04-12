<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDataHariLibur extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'master_data_hari_liburs';

    protected $fillable = [
        'periode_id',
        'tanggal',
        'keterangan',
        'tipe',
    ];

    public function periode()
    {
        return $this->belongsTo(MasterDataPeriode::class);
    }
}
