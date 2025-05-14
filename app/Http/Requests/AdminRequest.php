<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id'
        ];

        // Add password rule for store (create) request
        if ($this->isMethod('post')) {
            $rules['password'] = 'required|min:6';
        } else if ($this->isMethod('put') || $this->isMethod('patch')) {
            // For update requests, make password optional and modify email unique rule
            $rules['password'] = 'nullable|min:6';
            $rules['email'] = 'required|email|unique:admins,email,' . $this->route('admin')->id;
        }

        return $rules;
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The admin name is required.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'password.required' => 'The password is required for new admins.',
            'password.min' => 'The password must be at least 6 characters.',
            'permissions.required' => 'Please select at least one permission.',
            'permissions.min' => 'Please select at least one permission.',
            'permissions.*.exists' => 'One or more selected permissions are invalid.'
        ];
    }
} 