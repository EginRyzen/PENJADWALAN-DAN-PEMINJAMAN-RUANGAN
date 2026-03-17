<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataDocument extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'data_documents';

    protected $fillable = [
        'file_path',
        'file_name',
        'file_type',
        'checksum',
    ];

    public function document(){
        return $this->hasMany(DataBaseBuilding::class, 'building_image_id');
    }
    public function pengajuanRuangan()
    {
        return $this->hasMany(PengajuanRuangan::class, 'dokumen_pendukung_id');
    }
}
