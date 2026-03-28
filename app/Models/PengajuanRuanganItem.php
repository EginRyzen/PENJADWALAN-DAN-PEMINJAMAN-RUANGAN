<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanRuanganItem extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'pengajuan_ruangan_items';

    protected $fillable = [
        'pengajuan_id',
        'ruangan_id',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanRuangan::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(DataBaseBuildingRoom::class);
    }
}
