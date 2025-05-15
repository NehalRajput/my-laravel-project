<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1000',
            'receiver_id' => 'required|integer',
            'receiver_type' => 'required|string|in:App\\Models\\User,App\\Models\\Admin',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Message content is required.',
            'content.max' => 'Message cannot exceed 1000 characters.',
            'receiver_type.required' => 'Receiver type is required.',
            'receiver_type.in' => 'Invalid receiver type.',
            'receiver_id.required' => 'Receiver ID is required.',
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'Selected user does not exist.'
        ];
    }
} 