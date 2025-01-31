<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use App\Models\User;

class StoreUserRequest extends FormRequest
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
            'lastname' => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/'],
            'firstname' => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/'],
            'middlename' => ['nullable', 'string', 'regex:/^[a-zA-Z\s]+$/'],
            'extension_name' => ['nullable', 'string', 'regex:/^[a-zA-Z\s]+$/'],
            'contact_number' => ['required', 'regex:/^09\d{9}$/'],
            'address' => ['required', 'string'],
            'province_psgc' => ['required'],
            'city_name_psgc' => ['required'],
            'brgy_psgc' => ['required'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::min(8)->mixedCase()->numbers()->symbols()],
        ];
    }

    public function messages()
    {
        return [
            'lastname.required' => 'Please fill in this field.',
            'lastname.regex' => 'Invalid entry.',
            'firstname.required' => 'Please fill in this field.',
            'firstname.regex' => 'Invalid entry.',
            'middlename.regex' => 'Invalid entry.',

            'contact_number.required' => 'Please fill in this field.',
            'contact_number.regex' => 'Invalid entry.',

            'province_psgc.required' => 'Please select a province.',
            'city_name_psgc.required' => 'Please select a city.',
            'brgy_psgc.required' => 'Please select a barangay.',
            'address.required' => 'Please fill in this field.',

            'email.required' => 'Please fill in this field.',
            'email.email' => 'Invalid entry.',
            'email.unique' => 'Email already taken.',

            'password.required' => 'Please fill in this field.',
            'password.confirmed' => 'Password did not match.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.mixedCase' => 'Password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Password must contain at least one number.',
            'password.symbols' => 'Password must contain at least one special character.',

        ];
    }
}
