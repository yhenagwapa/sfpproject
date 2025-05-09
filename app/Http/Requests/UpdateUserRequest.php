<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use App\Models\User;


class UpdateUserRequest extends FormRequest
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
        $rules = [
            'lastname' => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/'],
            'firstname' => ['required', 'string', 'regex:/^[a-zA-Z\s]+$/'],
            'middlename' => ['nullable', 'string', 'regex:/^[a-zA-Z\s]+$/'],
            'extension_name' => ['nullable', 'string', 'regex:/^[a-zA-Z\s]+$/'],
            'contact_number' => ['required', 'regex:/^09\d{9}$/'],

            'address' => ['required', 'string'],
            'province_psgc' => ['required'],
            'city_name_psgc' => ['required'],
            'brgy_psgc' => ['required'],

            'email' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'confirmed'],
        ];

        // Only add the unique rule if the email has changed
        if ($this->filled('email') && $this->email !== auth()->user()->email) {
            $rules['email'][] = Rule::unique('users', 'email')->ignore($this->user_id);
        }

        // Only apply complex password rules if password is present
        if ($this->filled('password')) {
            $rules['password'][] = RulesPassword::min(8)->mixedCase()->numbers()->symbols();
        }

        return $rules;

    }

    public function messages()
    {
        return [
            'firstname.required' => 'Please fill in this field.',
            'firstname.regex' => 'Invalid entry.',
            'middlename.regex' => 'Invalid entry.',
            'lastname.required' => 'Please fill in this field.',
            'lastname.regex' => 'Invalid entry.',

            'contact_number.required' => 'Please fill in this field.',
            'contact_number.regex' => 'Invalid entry.',

            'address.required' => 'Please fill in this field.',
            'province_psgc.required' => 'Please select a province.',
            'city_name_psgc.required' => 'Please select a city.',
            'brgy_psgc.required' => 'Please select a barangay.',

            'email.required' => 'Please fill in this field.',
            'email.email' => 'Invalid entry.',
            'email.unique' => 'Email already taken.',

            'password.confirmed' => 'Password did not match.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.mixedCase' => 'Password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Password must contain at least one number.',
            'password.symbols' => 'Password must contain at least one special character.',
        ];
    }
}
