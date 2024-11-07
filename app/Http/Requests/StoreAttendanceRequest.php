<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('add-attendance');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $rules = [
            'attendance_date' => ['required', 'date'],
            'milk_attendance_date' => ['date'],
        ];
    
        $this->sometimes('milk_attendance_date', 'required', function ($input) {
            return !is_null($input->milk_feeding_id);
        });
    
        return $rules;
    }
    public function messages()
    {
        return [
            'attendance_date.required' => 'Please provide feeding date.',
            'milk_attendance_date.required' => 'Please provide milk feeding date.'
        ];
    }
}
