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
        $step = $this->input('step', 1);

        if ($step == 1) {
            return [
                'lastname' => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/'],
                'firstname' => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/'],
                'middlename' => ['nullable', 'string', 'regex:/^[a-zA-Z\s]+$/'],
                'extension_name' => ['nullable', 'string', 'regex:/^[a-zA-Z\s]+$/'],
                'date_of_birth' => ['required', 'date'],
                'sex_id' => ['required', 'exists:sexes,id'],
                'region_psgc' => ['required'],
                'province_psgc' => ['required'],
                'city_name_psgc' => ['required'],
                'brgy_psgc' => ['required'],
                'address' => ['required', 'string'],
                'pantawid_details' => ['nullable','required_if:is_pantawid,1'],
                'person_with_disability_details' => ['nullable','required_if:is_person_with_disability,1','string','max:255'],
                'is_indigenous_people' => ['required', 'boolean'],
                'is_child_of_soloparent' => ['required', 'boolean'],
                'is_lactose_intolerant' => ['required', 'boolean'],
            ];

        } elseif ($step == 2) {
            return [
                'child_development_center_id' => $this->input('action') === 'next'
                    ? ['required', 'exists:child_development_centers,id']
                    : ['nullable', 'exists:child_development_centers,id'],

                'implementation_id' => ['nullable', 'exists:implementations,id'],
                'milk_feeding_id' => ['nullable', 'exists:implementations,id'],
            ];
        }

        return [];
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

                'pantawid_details.required_if' => 'Please specify pantawid details.',
                'person_with_disability_details.required_if' => 'Please fill in disability details.',

                'child_development_center_id.required' => 'Please select CDC or SNP.',
            ];

    }


}
