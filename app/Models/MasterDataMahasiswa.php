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
        'semester',
        'angkatan',
        'status',
    ];

    public function programStudi()
    {
        return $this->belongsTo(MasterDataProgramStudi::class, 'program_studi_id');
    }
}
