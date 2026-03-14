<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterDataProgramStudi extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'master_data_program_studis';

    protected $fillable = [
        'kode',
        'nama',
        'fakultas',
        'jenjang',
        'status',
    ];
}
