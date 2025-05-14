<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('admin')->check();
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date|after:today',
            'interns' => 'nullable|array',
            'interns.*' => 'exists:users,id'
        ];

        if ($this->routeIs('*.status')) {
            $rules = [
                'status' => 'required|in:pending,todo,completed'
            ];
        }

        if ($this->routeIs('*.assign')) {
            $rules = [
                'intern_id' => 'required|exists:users,id'
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required.',
            'title.max' => 'Task title cannot exceed 255 characters.',
            'description.required' => 'Task description is required.',
            'due_date.date' => 'Invalid due date format.',
            'due_date.after' => 'Due date must be after today.',
            'interns.array' => 'Invalid interns selection.',
            'interns.*.exists' => 'One or more selected interns are invalid.',
            'status.required' => 'Task status is required.',
            'status.in' => 'Invalid task status selected.',
            'intern_id.required' => 'Intern selection is required.',
            'intern_id.exists' => 'Selected intern is invalid.'
        ];
    }
} 