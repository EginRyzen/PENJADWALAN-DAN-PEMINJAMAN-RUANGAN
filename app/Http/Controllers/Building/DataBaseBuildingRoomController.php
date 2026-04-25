<?php

namespace App\Http\Controllers\Building;

use App\Http\Controllers\Controller;
use App\Models\DataBaseBuildingRoom;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class DataBaseBuildingRoomController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = DataBaseBuildingRoom::query();

            if ($request->has('can_ujian')) {
                $query->where('can_ujian', $request->can_ujian);
            }

            // Tambahkan filter status gedung aktif
            $query->whereHas('building', function ($q) {
                $q->where('building_status', 'active');
            });

            $rooms = $query->get();
            return $this->successResponse($rooms, 'Daftar ruangan berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(DataBaseBuildingRoom $dataBaseBuildingRoom)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataBaseBuildingRoom $dataBaseBuildingRoom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataBaseBuildingRoom $dataBaseBuildingRoom)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataBaseBuildingRoom $dataBaseBuildingRoom)
    {
        //
    }

    /**
     * Get facilities of the specified room.
     */
    public function getFacilities($id)
    {
        try {
            $room = DataBaseBuildingRoom::with('facilities.facility')->find($id);

            if (!$room) {
                return $this->errorResponse('Ruangan tidak ditemukan', 404, 'Not Found');
            }

            return $this->successResponse(
                $room->facilities,
                'Fasilitas ruangan berhasil diambil'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }
}
