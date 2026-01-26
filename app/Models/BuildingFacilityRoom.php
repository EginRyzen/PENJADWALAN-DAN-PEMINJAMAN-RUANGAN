<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingFacilityRoom extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'building_facility_rooms';

    protected $fillable = [
        'facility_id',
        'room_id',
        'quantity',
    ];

    public function room()
    {
        return $this->belongsTo(DataBaseBuildingRoom::class, 'room_id');
    }

    public function facility()
    {
        return $this->belongsTo(DataBaseBuildingFacility::class, 'facility_id');
    }
}
