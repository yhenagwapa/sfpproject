<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChildDevelopmentCenterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create-child-development-center');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'center_name', ['required', 'string:255'],
            'assigned_user_id', ['required', 'exists:users,id'],
            'province_psgc' => ['required'],
            'city_name_psgc' => ['required'],
            'brgy_psgc' => ['required'],
            'address' => ['required', 'string'],
            'zip_code' => ['required', 'digits:4'],
        ];
    }
    public function messages()
    {
        return [
            'center_name.required' => 'Please fill in name of child development center',
            'center_name.string' => 'Invalid entry.',
            'assigned_user_id' => 'Please select assigned worker.',
            'province_psgc.required' => 'Please select a province.',
            'city_name_psgc.required' => 'Please select a city.',
            'brgy_psgc.required' => 'Please select a barangay.',
            'address.required' => 'Please fill in address.',
            'zip_code.required' => 'Please fill in zip code.',
            'zip_code.digits' => 'Invalid entry.',
        ];
    }

}
