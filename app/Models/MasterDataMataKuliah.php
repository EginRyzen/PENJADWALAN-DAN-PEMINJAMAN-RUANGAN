<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MasterDataMataKuliah extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'master_data_mata_kuliahs';

    protected $fillable = [
        'kode',
        'nama',
        'sks',
        'semester',
        'program_studi_id',
    ];

    public function programStudi()
    {
        return $this->belongsTo(MasterDataProgramStudi::class, 'program_studi_id');
    }
}
