<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataBaseBuilding extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'data_base_buildings';

    protected $fillable = [
        'building_name',
        'building_code',
        'building_location',
        'building_status',
        'building_image_id',
    ];

    public function image()
    {
        return $this->belongsTo(DataDocument::class, 'building_image_id');
    }

    public function rooms()
{
    return $this->hasMany(DataBaseBuildingRoom::class, 'building_id');
}
}
