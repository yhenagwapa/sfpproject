<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNutritionalStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('nutrition-status-exit');
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
            'exit_weight' => ['required', 'numeric'],
            'exit_height' => ['required', 'numeric'],
            'exit_actual_date_of_weighing' => ['required', 'date'],
        ];
    }
    public function messages()
    {
        return [
            'exit_weight.required' => 'Please fill in weight.',
            'exit_weight.numeric' => 'Invalid entry',
            'exit_height.required' => 'Please fill in weight.',
            'exit_height.numeric' => 'Invalid entry',
            'exit_actual_date_of_weighing.required' => 'Please fill in actual date of weighing',
        ];
    }
}
