<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChildRequest extends FormRequest
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
            'firstname' => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/'],
            'middlename' => ['nullable', 'string', 'regex:/^[a-zA-Z\s]+$/'],
            'lastname' => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/'],
            'extension_name' => ['nullable', 'string', 'regex:/^[a-zA-Z\s]+$/'],
            'date_of_birth' => ['required', 'date'],
            'sex' => ['required', 'string'],
            'actual_date_of_weighing' => ['required', 'date'],
            'weight' => ['required', 'integer'],
            'height' => ['required', 'integer'],
            'psgc_id' => ['required', 'exists:psgcs,psgc_id'],
            'address' => ['required', 'string'],
            'zip_code' => ['required', 'digits:4'],
            'child_development_center_id' => ['required', 'exists:child_development_centers,id'],
            'is_pantawid' => ['required', 'boolean'],
            'pantawid_details' => ['nullable','required_if:is_pantawid,1'],
            'is_person_with_disability' => ['required', 'boolean'],
            'person_with_disability_details' => ['nullable','required_if:is_pwd,1','string','max:255'],
            'is_indigenous_people' => ['required', 'boolean'],
            'is_child_of_soloparent' => ['required', 'boolean'],
            'is_lactose_intolerant' => ['required', 'boolean'],
            'deworming_date' => ['nullable', 'date'],
            'vitamin_a_date' => ['nullable', 'date'],
        ];
    }

    public function messages()
    {
        return [
            'firstname.required' => 'Please fill in this field.',
            'firstname.regex' => 'Invalid entry.',
            'middlename.regex' => 'Invalid entry.',
            'lastname.required' => 'Please fill in this field.',
            'lastname.regex' => 'Invalid entry.',
            'date_of_birth.required' => 'Please fill in this field.',
            'sex.required' => 'Please fill in this field.',
            'actual_date_of_weighing.required' => 'Please fill in this field.',
            'weight.required' => 'Please fill in this field.',
            'weight.integer' => 'Invalid entry.',
            'height.required' => 'Please fill in this field.',
            'height.integer' => 'Invalid entry.',
            'child_development_center_id.required' => 'Please select a child development center.',
            'province.required' => 'Please select a province.',
            'city.required' => 'Please select a city.',
            'barangay.required' => 'Please select a barangay.',
            'province' => ['required', 'exists:psgcs,province_psgc'],
            'city' => ['required', 'exists:psgcs,city_name_psgc'],
            'barangay' => ['required', 'exists:psgcs,brgy_psgc'],
            'address.required' => 'Please fill in this field.',
            'zip_code.required' => 'Please fill in this field.',
            'zip_code.digits' => 'Invalid entry.',
            'pantawid_details.required_if' => 'Please specify.',
            'person_with_disability_.required_if' => 'Please fill in this field.',

        ];
    }
}