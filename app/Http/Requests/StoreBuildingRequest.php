<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBuildingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ubah ke true agar request diizinkan
    }

    public function rules(): array
    {
        return [
            // DataBaseBuilding fields
            'building_name'     => 'required|string|max:255',
            'building_code'     => 'required|string|unique:data_base_buildings,building_code',
            'building_location' => 'required|string',
            'building_status'   => 'required|string',
            'building_image_id' => 'nullable|exists:data_documents,id',

            // DataBaseBuildingRoom fields
            'rooms'                 => 'required|array|min:1',
            'rooms.*.room_name'     => 'required|string',
            'rooms.*.room_code'     => 'required|string',
            'rooms.*.room_location' => 'required|string',
            'rooms.*.room_status'   => 'required|string',
            'rooms.*.room_capacity' => 'required|integer',
            'rooms.*.room_purpose'  => 'required|string',
            'rooms.*.can_ujian'     => 'required|boolean',
            'rooms.*.can_pembelajaran' => 'required|boolean',

            // BuildingFacilityRoom fields
            'rooms.*.facilities'             => 'nullable|array',
            'rooms.*.facilities.*.facility_id' => 'required|exists:data_base_building_facilities,id',
            'rooms.*.facilities.*.quantity'    => 'required|integer|min:1',
        ];
    }
}
