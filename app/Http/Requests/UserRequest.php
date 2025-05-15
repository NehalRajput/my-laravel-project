<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
        ];

        // Only require password for new users
        if ($this->isMethod('POST')) {
            $rules['password'] = ['required', 'string', Password::defaults()];
        } else {
            $rules['password'] = ['nullable', 'string', Password::defaults()];
            $rules['email'] = ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user->id];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.'
        ];
    }
} 