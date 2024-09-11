<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEntryNutritionalStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'weight' => ['required', 'numeric'],
            'height' => ['required', 'donumericuble'],
            'actual_date_of_weighing' => ['required', 'date'],
        ];
    }
    public function messages()
    {
        return [
            'weight.required' => 'Please fill in weight.',
            'weight.numeric' => 'Invalid entry',
            'date_of_birth.required' => 'Please fill in actual date of weighing',
        ];
    }
}
