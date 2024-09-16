<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChildDevelopmentCenterRequest extends FormRequest
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
            'center_name' => ['required', 'string', 'max:255'],
            'assigned_user_id' => ['required', 'exists:users,id'],
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
            'center_name.required' => 'Please fill in the name of the child development center.',
            'center_name.string' => 'Invalid entry for center name.',
            'assigned_user_id.required' => 'Please select an assigned worker.',
            'assigned_user_id.exists' => 'Selected worker does not exist.',
            'province_psgc.required' => 'Please select a province.',
            'city_name_psgc.required' => 'Please select a city.',
            'brgy_psgc.required' => 'Please select a barangay.',
            'address.required' => 'Please fill in the address.',
            'zip_code.required' => 'Please fill in the zip code.',
            'zip_code.digits' => 'Zip code must be 4 digits.',
        ];
    }
}
