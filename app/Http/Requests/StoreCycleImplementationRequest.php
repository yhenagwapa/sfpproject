<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCycleImplementationRequest extends FormRequest
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
            'cycle_name' => ['required','string:255'],
            'cycle_school_year' => ['required','string:9'],
            'cycle_target' => ['required','numeric'],
            'cycle_allocation' => ['required', 'regex:/^\d{1,12}(\.\d{2})?$/'],
            'cycle_status' => ['required','string:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'cycle_name.required' => 'Please fill in cycle name',
            'cycle_school_year.required' => 'Please fill in school year.',
            'cycle_target.required' => 'Please fill in target.',
            'cycle_target.numeric' => 'Invalid entry.',
            'cycle_allocation.required' => 'Please fill in allocation', 
            'cycle_status.required' => 'Please select a status.' 
        ];
    }
}