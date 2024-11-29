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
        return [
            'attendance_date' => [
                $this->isStoreCycleAttendanceRoute() ? 'required' : 'nullable',
                'date'
            ],
            'milk_attendance_date' => [
                $this->isStoreMilkeAttendanceRoute() ? 'required' : 'nullable',
                'date'
            ],
        ];
    }

    protected function isStoreCycleAttendanceRoute(): bool
    {
        return $this->routeIs('attendance.storeCycleAttendance');
    }

    protected function isStoreMilkeAttendanceRoute(): bool
    {
        return $this->routeIs('attendance.storeMilkAttendance');
    }
    public function messages()
    {
        return [
            'attendance_date.required' => 'Please provide feeding date.',
            'milk_attendance_date.required' => 'Please provide milk feeding date.'
        ];
    }
}
