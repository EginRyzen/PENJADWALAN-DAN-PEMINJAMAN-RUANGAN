<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MatserOperationalSchedule extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'matser_operational_schedules';

    protected $fillable = [
        'sks_setting_id',
        'day',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'status',
    ];

    public function sksSetting()
    {
        return $this->belongsTo(MasterSksSetting::class, 'sks_setting_id');
    }
}
