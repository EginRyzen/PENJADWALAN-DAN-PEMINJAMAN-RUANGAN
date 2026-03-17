<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MasterDataDosen extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'nidn',
        'nip',
        'nama',
        'program_studi_id',
        'jabatan',
        'status',
    ];

    public function programStudi()
    {
        return $this->belongsTo(MasterDataProgramStudi::class, 'program_studi_id');
    }
}
