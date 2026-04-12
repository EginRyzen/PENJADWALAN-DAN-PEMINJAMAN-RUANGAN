<?php

namespace App\Http\Requests\Pengajuan;

use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanPeminjamanRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tipe_pengajuan' => 'required|in:PEMBELAJARAN,EVENT',
            'tanggal_start' => 'required|date',
            'tanggal_end' => 'required|date|after_or_equal:tanggal_start',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'alasan' => 'required|string',
            'dokumen_pendukung_id' => 'nullable|uuid|exists:data_documents,id',
            'all_room_ids' => 'required|array|min:1',
            'all_room_ids.*' => 'required|uuid|exists:data_base_building_rooms,id',
            'items' => 'required|array|min:1',
            'items.*.building_id' => 'required|uuid|exists:data_base_buildings,id',
        ];
    }
}
