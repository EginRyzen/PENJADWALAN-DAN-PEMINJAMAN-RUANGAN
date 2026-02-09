<?php

namespace App\Http\Controllers\Building;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBuildingRequest;
use App\Models\BuildingFacilityRoom;
use App\Models\DataBaseBuilding;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DataBaseBuildingController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBuildingRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $building = DataBaseBuilding::create([
                    'building_name'     => $request->building_name,
                    'building_code'     => $request->building_code,
                    'building_location' => $request->building_location,
                    'building_status'   => $request->building_status,
                    'building_image_id' => $request->building_image_id,
                ]);

                if ($request->has('rooms')) {
                    foreach ($request->rooms as $roomData) {
                        $room = $building->rooms()->create([
                            'room_name'     => $roomData['room_name'],
                            'room_code'     => $roomData['room_code'],
                            'room_location' => $roomData['room_location'],
                            'room_status'   => $roomData['room_status'],
                            'room_capacity' => $roomData['room_capacity'],
                            'room_purpose'  => $roomData['room_purpose'],
                        ]);

                        if (!empty($roomData['facilities'])) {
                            foreach ($roomData['facilities'] as $facility) {
                                BuildingFacilityRoom::create([
                                    'room_id'     => $room->id,
                                    'facility_id' => $facility['facility_id'],
                                    'quantity'    => $facility['quantity'],
                                ]);
                            }
                        }
                    }
                }

                return $this->successResponse(
                    $building->load('rooms.facilities.facility', 'image'),
                    'Gedung berhasil dibuat',
                    201,
                    'Created'
                );
            });
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DataBaseBuilding $dataBaseBuilding)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataBaseBuilding $dataBaseBuilding)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataBaseBuilding $dataBaseBuilding)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataBaseBuilding $dataBaseBuilding)
    {
        //
    }
}
