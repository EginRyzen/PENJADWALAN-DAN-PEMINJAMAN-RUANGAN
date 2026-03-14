<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterDataProgramStudi extends FormRequest
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
        $id = $this->route('program_studi');

        return [
            'kode'     => 'required|string|max:20|unique:master_data_program_studis,kode,' . ($id ?? 'NULL') . ',id',
            'nama'     => 'required|string|max:100',
            'fakultas' => 'nullable|string|max:100',
            'jenjang'  => 'required|in:D3,D4,S1,S2,S3',
            'status'   => 'required|in:aktif,non-aktif',
        ];
    }

    public function messages(): array
    {
        return [
            'kode.required'   => 'Kode program studi wajib diisi.',
            'kode.unique'     => 'Kode program studi sudah digunakan.',
            'nama.required'   => 'Nama program studi wajib diisi.',
            'jenjang.required' => 'Jenjang wajib dipilih.',
            'jenjang.in'      => 'Jenjang tidak valid. Pilih: D3, D4, S1, S2, atau S3.',
            'status.required' => 'Status wajib dipilih.',
            'status.in'       => 'Status tidak valid. Pilih: aktif atau non-aktif.',
        ];
    }
}
