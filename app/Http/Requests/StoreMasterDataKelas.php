<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterDataKelas extends FormRequest
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
        return [
            'nama_kelas'       => 'required|string|max:100',
            'program_studi_id' => 'required|exists:master_data_program_studis,id',
            'periode_id'       => 'required|exists:master_data_periodes,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kelas.required'       => 'Nama kelas wajib diisi.',
            'program_studi_id.required' => 'Program studi wajib dipilih.',
            'program_studi_id.exists'   => 'Program studi tidak valid.',
            'periode_id.required'       => 'Periode wajib dipilih.',
            'periode_id.exists'         => 'Periode tidak valid.',
        ];
    }
}
