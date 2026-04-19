<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
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
        $id = $this->route('menu');

        return [
            'menu_code'    => 'required|string|max:10|unique:menus,menu_code,' . $id,
            'menu_name'    => 'required|string|max:100',
            'menu_id_alias'=> 'nullable|string|max:50',
            'menu_desc'    => 'nullable|string',
            'sequence'     => 'nullable|integer',
            'parent_id'    => 'nullable|uuid|exists:menus,id',
            'is_desktop'   => 'nullable|boolean',
            'is_mobile'    => 'nullable|boolean',
        ];
    }
}
