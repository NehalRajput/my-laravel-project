<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatRequest extends FormRequest
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
            'message' => 'required|string|max:1000',
            'recipient_id' => 'required|exists:users,id',
            'attachment' => 'nullable|file|max:10240', // 10MB max file size
            'message_type' => 'required|in:text,file,image',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'message.required' => 'Please enter a message.',
            'message.max' => 'Message cannot exceed 1000 characters.',
            'recipient_id.required' => 'Recipient is required.',
            'recipient_id.exists' => 'Selected recipient is invalid.',
            'attachment.file' => 'The attachment must be a valid file.',
            'attachment.max' => 'File size cannot exceed 10MB.',
            'message_type.required' => 'Message type is required.',
            'message_type.in' => 'Invalid message type specified.',
        ];
    }
} 