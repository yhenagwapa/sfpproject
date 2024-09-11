<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExitNutritionalStatusRequest extends FormRequest
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
            'child_id' => ['required', 'exists:children,id'],
            'weight' => ['required', 'numeric'],
            'height' => ['required', 'numeric'],
            'actual_date_of_weighing' => ['required', 'date'],
        ];
    }
    public function messages()
    {
        return [
            'weight.required' => 'Please fill in weight.',
            'weight.numeric' => 'Invalid entry',
            'height.required' => 'Please fill in weight.',
            'height.numeric' => 'Invalid entry',
            'actual_date_of_weighing.required' => 'Please fill in actual date of weighing',
        ];
    }
}
