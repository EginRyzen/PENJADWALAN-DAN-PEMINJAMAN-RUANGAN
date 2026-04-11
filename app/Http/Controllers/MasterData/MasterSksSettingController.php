<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMasterSksSetting;
use App\Models\MasterSksSetting;
use App\Models\MatserOperationalSchedule;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MasterSksSettingController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $type = $request->query('type', 'pelajaran');
            
            $setting = MasterSksSetting::with(['operationalSchedules' => function($q) {
                // Ensure days are ordered logically if needed, but the UI handles it
                $q->orderBy('day', 'asc');
            }])
            ->where('type', $type)
            ->first();

            if (!$setting) {
                return $this->errorResponse('Konfigurasi tidak ditemukan', 404, 'Not Found');
            }

            return $this->successResponse($setting, 'Konfigurasi SKS berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMasterSksSetting $request, string $id)
    {
        try {
            $setting = MasterSksSetting::find($id);

            if (!$setting) {
                return $this->errorResponse('Konfigurasi tidak ditemukan', 404, 'Not Found');
            }

            $validated = $request->validated();

            DB::beginTransaction();

            // Update main setting
            $setting->update([
                'duration_minutes' => $validated['duration_minutes']
            ]);

            // Update schedules only if provided
            if (!empty($validated['schedules'])) {
                foreach ($validated['schedules'] as $schedData) {
                    MatserOperationalSchedule::where('id', $schedData['id'])
                        ->where('sks_setting_id', $id)
                        ->update([
                            'start_time'  => $schedData['start_time'],
                            'end_time'    => $schedData['end_time'],
                            'break_start' => $schedData['break_start'],
                            'break_end'   => $schedData['break_end'],
                            'status'      => $schedData['status'],
                        ]);
                }
            }

            DB::commit();

            return $this->successResponse($setting->load('operationalSchedules'), 'Konfigurasi berhasil diperbarui');
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 422, 'Unprocessable Content', $e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }
}
