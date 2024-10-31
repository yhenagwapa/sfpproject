<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChildRequest extends FormRequest
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
            'sex_id' => ['required', 'exists:sexes,id'],
            // 'psgc_id' => ['required', 'exists:psgcs,psgc_id'],
            'province_psgc' => ['required'],
            'city_name_psgc' => ['required'],
            'brgy_psgc' => ['required'],
            'address' => ['required', 'string'],
            'zip_code' => ['required', 'digits:4'],
            'is_pantawid' => ['required', 'boolean'],
            'pantawid_details' => ['nullable','required_if:is_pantawid,1'],
            'is_person_with_disability' => ['required', 'boolean'],
            'person_with_disability_details' => ['nullable','required_if:is_person_with_disability,1','string','max:255'],
            'is_indigenous_people' => ['required', 'boolean'],
            'is_child_of_soloparent' => ['required', 'boolean'],
            'is_lactose_intolerant' => ['required', 'boolean'],
            'deworming_date' => ['nullable', 'date'],
            'vitamin_a_date' => ['nullable', 'date'],
            'child_development_center_id' => ['required', 'exists:child_development_centers,id'],
            'cycle_implementation_id' => ['nullable', 'exists:cycle_implementations,id'],
            'milk_feeding_id' => ['nullable', 'exists:milk_feedings,id'],
            'is_funded' => ['nullable', 'boolean'],
        ];
    }
    public function messages()
    {
        return [
            'firstname.required' => 'Please fill in first name.',
            'firstname.regex' => 'Invalid entry.',
            'middlename.regex' => 'Invalid entry.',
            'lastname.required' => 'Please fill in last name.',
            'lastname.regex' => 'Invalid entry.',
            'date_of_birth.required' => 'Please select date of birth.',
            'sex_id.required' => 'Please select sex.',

            'province_psgc.required' => 'Please select a province.',
            'city_name_psgc.required' => 'Please select a city.',
            'brgy_psgc.required' => 'Please select a barangay.',
            'address.required' => 'Please fill in address.',
            'zip_code.required' => 'Please fill in zip code.',
            'zip_code.digits' => 'Invalid entry.',
            
            'pantawid_details.required_if' => 'Please specify pantawid details.',
            'person_with_disability_details.required_if' => 'Please fill in disability details.',

            'child_development_center_id.required' => 'Please select a CDC or SNP.'
        ];
    }

    
}
