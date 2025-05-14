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
        return true; // Assuming we want logged-in users to be able to send messages
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required_without:attachment|string|max:1000',
            'message_type' => 'required|string|in:text,file',
            'attachment' => 'nullable|file|max:10240', // Max 10MB file size
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'recipient_id.required' => 'Please select a recipient for your message.',
            'recipient_id.exists' => 'The selected recipient does not exist.',
            'message.required_without' => 'Please provide either a message or an attachment.',
            'message.max' => 'The message cannot be longer than 1000 characters.',
            'message_type.required' => 'Message type is required.',
            'message_type.in' => 'Invalid message type.',
            'attachment.max' => 'The attachment must not be larger than 10MB.',
        ];
    }
} 