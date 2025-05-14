<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InternRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'intern';
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,todo,completed',
            'task_id' => 'required|exists:tasks,id'
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Task status is required.',
            'status.in' => 'Invalid task status selected.',
            'task_id.required' => 'Task ID is required.',
            'task_id.exists' => 'Selected task does not exist.'
        ];
    }
} 