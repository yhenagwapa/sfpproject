<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMilkFeedingRequest extends FormRequest
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
            'name' => ['required','string:255'],
            'school_year' => ['required','string:9'],
            'target' => ['required','numeric'],
            'allocation' => ['required', 'regex:/^\d{1,12}(\.\d{2})?$/'],
            'status' => ['required','string:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please fill in cycle name',
            'school_year.required' => 'Please fill in school year.',
            'target.required' => 'Please fill in target.',
            'target.numeric' => 'Invalid entry.',
            'allocation.required' => 'Please fill in allocation', 
            'status.required' => 'Please select a status.' 
        ];
    }
}
