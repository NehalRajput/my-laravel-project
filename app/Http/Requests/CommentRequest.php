<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // For update and delete, only admin can perform these actions
        if (in_array($this->method(), ['PUT', 'PATCH', 'DELETE'])) {
            return Auth::guard('admin')->check();
        }
        
        // For other operations (create), any authenticated user can perform
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'content' => 'required|string|max:1000',
            'is_query' => 'boolean',
        ];

        if ($this->isMethod('POST')) {
            $rules['task_id'] = 'required|exists:tasks,id';
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
            'content.required' => 'Comment content is required.',
            'content.max' => 'Comment cannot exceed 1000 characters.',
            'task_id.required' => 'Task ID is required.',
            'task_id.exists' => 'Selected task does not exist.',
            'is_query.boolean' => 'Invalid query flag value.',
        ];
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException('You are not authorized to perform this action.');
    }
} 