<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMasterOperasionalSchedule extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Check if it's a bulk update (look for schedules array)
        if ($this->has('schedules')) {
            return [
                'schedules' => 'required|array',
                'schedules.*.id' => 'required|uuid|exists:matser_operational_schedules,id',
                'schedules.*.start_time' => 'required',
                'schedules.*.end_time' => 'required',
                'schedules.*.break_start' => 'required',
                'schedules.*.break_end' => 'required',
                'schedules.*.status' => 'required|in:aktif,non-aktif',
            ];
        }

        return [
            'start_time' => 'required',
            'end_time' => 'required',
            'break_start' => 'required',
            'break_end' => 'required',
            'status' => 'required|in:aktif,non-aktif',
        ];
    }
}
