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
        return auth()->user()->can('nutrition-status-entry');
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
            'entry_weight' => ['required', 'numeric'],
            'entry_height' => ['required', 'numeric'],
            'entry_actual_date_of_weighing' => ['required', 'date'],
        ];
    }
    public function messages()
    {
        return [
            'entry_weight.required' => 'Please fill in weight.',
            'entry_weight.numeric' => 'Invalid entry',
            'entry_height.required' => 'Please fill in weight.',
            'entry_height.numeric' => 'Invalid entry',
            'entry_actual_date_of_weighing.required' => 'Please fill in actual date of weighing',
        ];
    }
}
