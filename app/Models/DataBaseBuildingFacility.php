<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataBaseBuildingFacility extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'data_base_building_facilities';

    protected $fillable = [
        'facility_name',
    ];

    public function rooms()
    {
        return $this->hasMany(BuildingFacilityRoom::class);
    }
}
