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

    public function kelas()
    {
        return $this->belongsTo(MasterDataKelas::class, 'kelas_id');
    }

    public function mataKuliah()
    {
        return $this->belongsTo(MasterDataMataKuliah::class, 'mata_kuliah_id');
    }
}
