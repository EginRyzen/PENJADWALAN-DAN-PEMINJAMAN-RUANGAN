<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterDataMahasiswa extends FormRequest
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
        $id = $this->route('mahasiswa');

        return [
            'nim'              => 'required|string|max:20|unique:master_data_mahasiswas,nim,' . ($id ?? 'NULL') . ',id',
            'nama'             => 'required|string|max:255',
            'program_studi_id' => 'required|uuid|exists:master_data_program_studis,id',
            'semester'         => 'required|integer|min:1|max:14',
            'angkatan'         => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'status'           => 'required|in:Aktif,Non-Aktif,Cuti,Lulus',
        ];
    }

    public function messages(): array
    {
        return [
            'nim.required'              => 'NIM wajib diisi.',
            'nim.unique'                => 'NIM sudah digunakan.',
            'nama.required'             => 'Nama lengkap wajib diisi.',
            'program_studi_id.required' => 'Program studi wajib dipilih.',
            'program_studi_id.exists'   => 'Program studi tidak valid.',
            'semester.required'         => 'Semester wajib diisi.',
            'angkatan.required'         => 'Angkatan wajib diisi.',
            'status.required'           => 'Status wajib dipilih.',
            'status.in'                 => 'Status tidak valid. Pilih: Aktif, Non-Aktif, Cuti, atau Lulus.',
        ];
    }
}
