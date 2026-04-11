<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MasterDataMahasiswa extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'master_data_mahasiswas';

    protected $fillable = [
        'nim',
        'nama',
        'program_studi_id',
        'kelas_id',
        'semester',
        'periode_id',
        'status',
    ];

    public function programStudi()
    {
        return $this->belongsTo(MasterDataProgramStudi::class, 'program_studi_id');
    }

    public function kelas()
    {
        return $this->belongsTo(MasterDataKelas::class, 'kelas_id');
    }

    public function periode()
    {
        return $this->belongsTo(MasterDataPeriode::class, 'periode_id');
    }
}
