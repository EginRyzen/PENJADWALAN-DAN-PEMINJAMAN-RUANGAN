<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMasterDataHariLibur extends FormRequest
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
        $id = $this->route('hari_libur');

        return [
            'periode_id' => 'nullable|exists:master_data_periodes,id',
            'tanggal'    => [
                'required',
                'date',
                Rule::unique('master_data_hari_liburs', 'tanggal')->ignore($id),
            ],
            'keterangan' => 'required|string|max:255',
            'tipe'       => 'required|in:nasional,kampus',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'tanggal.required'    => 'Tanggal hari libur wajib diisi.',
            'tanggal.date'        => 'Format tanggal tidak valid.',
            'tanggal.unique'      => 'Tanggal ini sudah terdaftar sebagai hari libur.',
            'keterangan.required' => 'Keterangan hari libur wajib diisi.',
            'tipe.required'       => 'Tipe hari libur wajib dipilih.',
            'tipe.in'             => 'Tipe hari libur tidak valid.',
            'periode_id.exists'   => 'Periode tidak ditemukan.',
        ];
    }
}
