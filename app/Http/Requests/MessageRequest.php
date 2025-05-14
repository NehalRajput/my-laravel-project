<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $rules = [
            'content' => 'required|string|max:5000',
        ];

        if ($this->isMethod('POST')) {
            $rules['receiver_type'] = 'required|string|in:App\\Models\\Admin,App\\Models\\User';
            $rules['receiver_id'] = 'required|integer|exists:' . 
                                  (str_contains($this->receiver_type, 'Admin') ? 'admins' : 'users') . 
                                  ',id';
        }

        if ($this->routeIs('messages.get')) {
            $rules = [
                'user_id' => 'required|exists:users,id'
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Message content is required.',
            'content.max' => 'Message cannot exceed 5000 characters.',
            'receiver_type.required' => 'Receiver type is required.',
            'receiver_type.in' => 'Invalid receiver type.',
            'receiver_id.required' => 'Receiver ID is required.',
            'receiver_id.exists' => 'Selected receiver does not exist.',
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'Selected user does not exist.'
        ];
    }
} 