<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MasterDataPeriode extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'master_data_periodes';

    protected $fillable = [
        'nama',
        'start_date',
        'end_date',
        'status',
    ];

    public function kelas()
    {
        return $this->hasMany(MasterDataKelas::class, 'periode_id');
    }

    public function mahasiswas()
    {
        return $this->hasMany(MasterDataMahasiswa::class, 'periode_id');
    }

}
