<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MasterDataKelas extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'master_data_kelas';

    protected $fillable = [
        'nama_kelas',
        'program_studi_id',
        'periode_id',
    ];

    public function programStudi()
    {
        return $this->belongsTo(MasterDataProgramStudi::class, 'program_studi_id');
    }

    public function mahasiswas()
    {
        return $this->hasMany(MasterDataMahasiswa::class, 'kelas_id');
    }

    public function periode()
    {
        return $this->belongsTo(MasterDataPeriode::class, 'periode_id');
    }

    public function mataKuliahs()
    {
        return $this->belongsToMany(MasterDataMataKuliah::class, 'master_data_kelas_mata_kuliahs', 'kelas_id', 'mata_kuliah_id')
            ->withPivot('semester')
            ->withTimestamps();
    }
}
