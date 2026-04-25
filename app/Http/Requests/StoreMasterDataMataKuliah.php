<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterDataMataKuliah extends FormRequest
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
        $id = $this->route('mata_kuliah'); // Assuming the resource name is mata-kuliah

        return [
            'kode'             => 'required|string|max:20|unique:master_data_mata_kuliahs,kode,' . ($id ?? 'NULL') . ',id',
            'nama'             => 'required|string|max:255',
            'sks'              => 'required|integer|min:1|max:10',
            'semester'         => 'required|integer|min:1|max:14',
            'sks_ujian'        => 'nullable|integer|min:0|max:10',
            'program_studi_id' => 'required|uuid|exists:master_data_program_studis,id',
        ];
    }

    public function messages(): array
    {
        return [
            'kode.required'             => 'Kode mata kuliah wajib diisi.',
            'kode.unique'               => 'Kode mata kuliah sudah digunakan.',
            'nama.required'             => 'Nama mata kuliah wajib diisi.',
            'sks.required'              => 'SKS wajib diisi.',
            'sks.integer'               => 'SKS harus berupa angka.',
            'sks_ujian.integer'         => 'SKS Ujian harus berupa angka.',
            'semester.required'         => 'Semester wajib diisi.',
            'semester.integer'          => 'Semester harus berupa angka.',
            'program_studi_id.required' => 'Program studi wajib dipilih.',
            'program_studi_id.exists'   => 'Program studi tidak valid.',
        ];
    }
}
