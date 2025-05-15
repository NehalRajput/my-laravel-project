<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InternTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only allow interns to make this request
        return Auth::check() && Auth::user()->hasRole('intern');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'task_id' => 'required|exists:tasks,id',
            'content' => 'required|string|max:1000',
            'status' => 'sometimes|string|in:pending,in_progress,completed,review',
            'comment' => 'sometimes|string|max:1000',
            'attachment' => 'nullable|file|max:5120', // Max 5MB file size
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
            'task_id.required' => 'Task ID is required.',
            'task_id.exists' => 'The selected task does not exist.',
            'content.required' => 'Content is required.',
            'content.max' => 'Content cannot be longer than 1000 characters.',
            'status.in' => 'Invalid task status.',
            'comment.max' => 'Comment cannot be longer than 1000 characters.',
            'attachment.max' => 'The attachment must not be larger than 5MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->status) {
            $this->merge([
                'status' => strtolower($this->status),
            ]);
        }
    }
} 