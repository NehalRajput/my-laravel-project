<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InternTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->route('task')) {
            return Auth::check() && 
                   Auth::user()->role === 'intern' && 
                   $this->route('task')->interns->contains(Auth::id());
        }
        return Auth::check() && Auth::user()->role === 'intern';
    }

    public function rules(): array
    {
        $rules = [
            'content' => 'required|string|max:1000',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['status'] = 'required|in:pending,todo,completed';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Comment content is required.',
            'content.max' => 'Comment cannot exceed 1000 characters.',
            'status.required' => 'Task status is required.',
            'status.in' => 'Invalid task status selected.'
        ];
    }
} 