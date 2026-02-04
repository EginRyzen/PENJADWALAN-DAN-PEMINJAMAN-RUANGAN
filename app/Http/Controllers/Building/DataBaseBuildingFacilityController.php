<?php

namespace App\Http\Controllers\Building;

use App\Http\Controllers\Controller;
use App\Models\DataBaseBuildingFacility;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class DataBaseBuildingFacilityController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $facilities = DataBaseBuildingFacility::orderBy('facility_name', 'asc')->get();

            return $this->successResponse(
                $facilities,
                'Daftar fasilitas berhasil diambil'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                500,
                'Internal Server Error'
            );
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
    public function show(DataBaseBuildingFacility $dataBaseBuildingFacility)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataBaseBuildingFacility $dataBaseBuildingFacility)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataBaseBuildingFacility $dataBaseBuildingFacility)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataBaseBuildingFacility $dataBaseBuildingFacility)
    {
        //
    }
}
