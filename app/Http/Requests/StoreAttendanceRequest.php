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
                $this->isStoreAttendanceRoute() ? 'required' : 'nullable',
                'date'
            ],
            'milk_attendance_date' => [
                $this->isStorMilkeAttendanceRoute() ? 'required' : 'nullable',
                'date'
            ],
        ];
    }

    protected function isStoreAttendanceRoute(): bool
    {
        return $this->routeIs('attendance.storeAttendance');
    }

    protected function isStorMilkeAttendanceRoute(): bool
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
