<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MasterSksSetting extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'master_sks_settings';

    protected $fillable = [
        'duration_minutes',
        'type',
        'status',
    ];

    public function operationalSchedules()
    {
        return $this->hasMany(MatserOperationalSchedule::class, 'sks_setting_id');
    }
}
