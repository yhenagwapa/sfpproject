<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNutritionalStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create-nutritional-status');
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
            'weighing_date' => ['required', 'date'],
        ];
    }
    public function messages()
    {
        return [
            'weight.required' => 'Please fill in weight.',
            'weight.numeric' => 'Invalid entry',
            'height.required' => 'Please fill in weight.',
            'height.numeric' => 'Invalid entry',
            'weighing_date.required' => 'Please fill in actual date of weighing',
        ];
    }
}
