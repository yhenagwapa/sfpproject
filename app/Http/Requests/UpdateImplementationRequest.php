<?php

namespace App\Http\Requests;

use App\Models\Implementation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateImplementationRequest extends FormRequest
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
            'cycle_target' => ['required','numeric'],
            'cycle_allocation' => ['required', 'regex:/^\d{1,12}(\.\d{2})?$/'],
            'cycle_school_year_from' => ['required', 'digits:4', 'integer', 'min:' . date('Y')],
            'cycle_type' => ['required','string:255'],
            'cycle_status' => ['required','string:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'cycle_name.required' => 'Please fill in cycle name',
            'cycle_target.required' => 'Please fill in target.',
            'cycle_target.numeric' => 'Invalid entry.',
            'cycle_allocation.required' => 'Please fill in allocation',
            'cycle_allocation.regex' => 'Invalid entry.',
            'cycle_school_from.required' => 'Please fill in school year from.',
            'cycle_school_from.min' => 'Minimum calendar year is ' . date('Y') . '.',
            'cycle_type.required' => 'Please select type.',
            'cycle_status.required' => 'Please select a status.'
        ];
    }
}
