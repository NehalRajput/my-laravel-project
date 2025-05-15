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
        // For update and delete, only admin is authorized
        if (in_array($this->method(), ['PUT', 'PATCH', 'DELETE'])) {
            return Auth::guard('admin')->check();
        }
        
        // For other operations (create), any authenticated user is authorized
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'content' => 'required|string|max:1000',
            'is_query' => 'boolean',
        ];

        // If this is a store request, task_id is required
        if ($this->isMethod('POST')) {
            $rules['task_id'] = 'required|exists:tasks,id';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'content.required' => 'Comment content is required.',
            'content.max' => 'Comment cannot be longer than 1000 characters.',
            'task_id.required' => 'Task ID is required.',
            'task_id.exists' => 'The selected task does not exist.',
            'is_query.boolean' => 'Is query field must be true or false.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        if ($this->has('is_query')) {
            $this->merge([
                'is_query' => $this->boolean('is_query'),
            ]);
        }
    }
} 