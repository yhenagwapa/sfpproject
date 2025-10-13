<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class UpdateChildRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('edit-child') || $this->getRequest()->attributes->get('temp_can_edit') === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $minDate = Carbon::now()->subYears(6)->addDay()->format('m-d-Y');
        $maxDate = Carbon::create(null, 6, 30)->subYears(2)->format('m-d-Y');

        if(auth()->user()->hasAnyRole(['lgu focal', 'sfp coordinator'])){
            return [];
        }

        if(auth()->user()->hasRole('child development worker') || auth()->user()->hasRole('encoder')){
            return [
                'lastname' => ['required', 'string', 'regex:/^[a-zA-ZÑñ0-9\s.-]+$/'],
                'firstname' => ['required', 'string', 'regex:/^[a-zA-ZÑñ0-9\s.-]+$/'],
                'middlename' => ['nullable', 'string', 'regex:/^[a-zA-ZÑñ0-9\s.-]+$/'],
                'extension_name' => ['nullable', 'string', 'regex:/^[a-zA-ZÑñ0-9\s.-]+$/'],
                'date_of_birth' => ['required', 'date_format:m-d-Y', 'after_or_equal:' . $minDate, 'before_or_equal:' . $maxDate,],
                'sex_id' => ['required', 'string'],
                'province_psgc' => ['required'],
                'city_name_psgc' => ['required'],
                'brgy_psgc' => ['required'],
                'address' => ['required', 'string'],
                'pantawid_details' => ['nullable','required_if:is_pantawid,1'],
                'person_with_disability_details' => ['nullable','required_if:is_person_with_disability,1','string','max:255'],
                'is_indigenous_people' => ['required', 'boolean'],
                'is_child_of_soloparent' => ['required', 'boolean'],
                'is_lactose_intolerant' => ['required', 'boolean'],
                'is_funded' => ['required', 'boolean'],
            ];
        }else {
            return [
                'lastname' => ['required', 'string', 'regex:/^[a-zA-ZÑñ0-9\s.-]+$/'],
                'firstname' => ['required', 'string', 'regex:/^[a-zA-ZÑñ0-9\s.-]+$/'],
                'middlename' => ['nullable', 'string', 'regex:/^[a-zA-ZÑñ0-9\s.-]+$/'],
                'extension_name' => ['nullable', 'string', 'regex:/^[a-zA-ZÑñ0-9\s.-]+$/'],
                'date_of_birth' => ['required', 'date_format:m-d-Y', 'after_or_equal:' . $minDate, 'before_or_equal:' . $maxDate,],
                'sex_id' => ['required', 'string'],
                'province_psgc' => ['required'],
                'city_name_psgc' => ['required'],
                'brgy_psgc' => ['required'],
                'address' => ['required', 'string'],
                'pantawid_details' => ['nullable','required_if:is_pantawid,1'],
                'person_with_disability_details' => ['nullable','required_if:is_person_with_disability,1','string','max:255'],
                'is_indigenous_people' => ['required', 'boolean'],
                'is_child_of_soloparent' => ['required', 'boolean'],
                'is_lactose_intolerant' => ['required', 'boolean'],
                'implementation_id' => ['nullable', 'exists:implementations,id'],
                'is_funded' => ['required', 'boolean'],
            ];
        }
    }

    public function messages()
    {
        $minDate = Carbon::now()->subYears(6)->addDay()->format('m-d-Y');
        $maxDate = Carbon::create(null, 6, 30)->subYears(2)->format('m-d-Y');

        return [
            'lastname.required' => 'Please fill in this field.',
            'lastname.regex' => 'This field only accepts letters, numbers and characters (.) and (-).',
            'firstname.required' => 'Please fill in this field.',
            'firstname.regex' => 'This field only accepts letters, numbers and characters (.) and (-).',
            'middlename.regex' => 'This field only accepts letters, numbers and characters (.) and (-).',
            'date_of_birth.required' => 'Please fill in this field.',
            'date_of_birth.after_or_equal' => 'Invalid date. Date of birth should be ' . $minDate . ' to ' . $maxDate . '.',
            'date_of_birth.before_or_equal' => 'Invalid date. Date of birth should be ' . $minDate . ' to ' . $maxDate . '.',
            'sex_id.required' => 'Please fill in this field.',

            'province.required' => 'Please select a province.',
            'city.required' => 'Please select a city.',
            'barangay.required' => 'Please select a barangay.',
            'address.required' => 'Please fill in this field.',

            'is_pantawid.required' => 'Please select an option.',
            'is_person_with_disability.required' => 'Please select an option.',
            'pantawid_details.required_if' => 'Please specify pantawid details.',
            'person_with_disability_details.required_if' => 'Please fill in disability details.',
            'is_indigenous_people.required' => 'Please select an option.',
            'is_child_of_soloparent.required' => 'Please select an option.',
            'is_lactose_intolerant.required' => 'Please select an option.',

            'is_funded.required' => 'Please select an option.',
        ];
    }
}
