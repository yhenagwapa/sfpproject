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
        return auth()->user()->can('edit-nutritional-status');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->input('form_type') === 'entry') {
            return [
                'child_id' => ['required', 'exists:children,id'],
                'weight' => ['required', 'numeric'],
                'height' => ['required', 'numeric'],
                'weighing_date' => ['required', 'date'],
            ];
        } elseif($this->input('form_type') === 'exit') {
            return [
                'exitchild_id' => ['required', 'exists:children,id'],
                'exitweight' => ['required', 'numeric'],
                'exitheight' => ['required', 'numeric'],
                'exitweighing_date' => ['required', 'date'],
            ];
        }
        return [];
    }
    public function messages()
    {
        return [
            'weight.required' => 'Please fill in weight.',
            'weight.numeric' => 'Invalid entry',
            'height.required' => 'Please fill in weight.',
            'height.numeric' => 'Invalid entry',
            'weighing_date.required' => 'Please fill in actual date of weighing',

            'exitweight.required' => 'Please fill in weight.',
            'exitweight.numeric' => 'Invalid entry',
            'exitheight.required' => 'Please fill in weight.',
            'exitheight.numeric' => 'Invalid entry',
            'exitweighing_date.required' => 'Please fill in actual date of weighing',
        ];
    }
}
