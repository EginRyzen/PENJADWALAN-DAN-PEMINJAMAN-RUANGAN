<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterDataDosen extends FormRequest
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
            'nidn'             => 'required|string|unique:master_data_dosens,nidn,' . $this->route('dosen'),
            'nip'              => 'nullable|string|unique:master_data_dosens,nip,' . $this->route('dosen'),
            'nama'             => 'required|string|max:255',
            'program_studi_id' => 'required|exists:master_data_program_studis,id',
            'jabatan'          => 'required|string',
            'status'           => 'required|string',
        ];
    }
}
