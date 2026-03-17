<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataBaseBuildingRoom extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'data_base_building_rooms';

    protected $fillable = [
        'building_id',
        'room_name',
        'room_code',
        'room_location',
        'room_status',
        'room_capacity',
        'room_purpose',
    ];

    public function building()
    {
        return $this->belongsTo(DataBaseBuilding::class, 'building_id');
    }
    public function facilities()
    {
        return $this->hasMany(BuildingFacilityRoom::class, 'room_id');
    }
    public function pengajuanRuangan()
    {
        return $this->hasMany(PengajuanRuangan::class, 'ruangan_id');
    }
}
