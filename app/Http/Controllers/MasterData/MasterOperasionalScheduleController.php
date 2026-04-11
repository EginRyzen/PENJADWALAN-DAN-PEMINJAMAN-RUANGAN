<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMasterOperasionalSchedule;
use App\Models\MasterSksSetting;
use App\Models\MatserOperationalSchedule;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MasterOperasionalScheduleController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $type = $request->query('type', 'pelajaran');
            
            $schedules = MatserOperationalSchedule::whereHas('sksSetting', function($q) use ($type) {
                $q->where('type', $type);
            })
            ->orderBy('day', 'asc')
            ->get();

            return $this->successResponse($schedules, 'Jadwal operasional berhasil diambil');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMasterOperasionalSchedule $request, string $id)
    {
        try {
            $schedule = MatserOperationalSchedule::find($id);

            if (!$schedule) {
                return $this->errorResponse('Jadwal tidak ditemukan', 404, 'Not Found');
            }

            $schedule->update($request->validated());

            return $this->successResponse($schedule, 'Jadwal berhasil diperbarui');
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 422, 'Unprocessable Content', $e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }

    /**
     * Bulk update schedules.
     */
    public function bulkUpdate(UpdateMasterOperasionalSchedule $request)
    {
        try {
            $validated = $request->validated();
            
            DB::beginTransaction();

            foreach ($validated['schedules'] as $data) {
                MatserOperationalSchedule::where('id', $data['id'])->update([
                    'start_time'  => $data['start_time'],
                    'end_time'    => $data['end_time'],
                    'break_start' => $data['break_start'],
                    'break_end'   => $data['break_end'],
                    'status'      => $data['status'],
                ]);
            }

            DB::commit();

            return $this->successResponse(null, 'Semua jadwal berhasil diperbarui');
        } catch (ValidationException $e) {
            return $this->errorResponse($e->getMessage(), 422, 'Unprocessable Content', $e->errors());
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage(), 500, 'Internal Server Error');
        }
    }
}
