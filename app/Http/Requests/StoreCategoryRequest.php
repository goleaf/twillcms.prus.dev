<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // No authentication as per requirements
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2',
                Rule::unique('categories', 'name')
            ],
            'title' => [
                'nullable',
                'string',
                'max:255',
                'min:2'
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'color' => [
                'nullable',
                'string',
                'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'
            ],
            'is_active' => [
                'boolean'
            ],
            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999'
            ],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:categories,id'
            ]
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The category name is required.',
            'name.unique' => 'A category with this name already exists.',
            'name.min' => 'The category name must be at least 2 characters.',
            'name.max' => 'The category name may not be greater than 255 characters.',
            
            'title.min' => 'The category title must be at least 2 characters.',
            'title.max' => 'The category title may not be greater than 255 characters.',
            
            'description.max' => 'The description may not be greater than 1000 characters.',
            
            'color.regex' => 'The color must be a valid hex color code (e.g., #FF5733).',
            
            'is_active.boolean' => 'The active status must be true or false.',
            
            'sort_order.integer' => 'The sort order must be a number.',
            'sort_order.min' => 'The sort order must be at least 0.',
            'sort_order.max' => 'The sort order may not be greater than 9999.',
            
            'parent_id.integer' => 'The parent category must be a valid number.',
            'parent_id.exists' => 'The selected parent category does not exist.'
        ];
    }

    /**
     * Get custom attribute names for error messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'category name',
            'title' => 'category title',
            'description' => 'category description',
            'color' => 'category color',
            'is_active' => 'active status',
            'sort_order' => 'sort order',
            'parent_id' => 'parent category'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Generate slug from name if not provided
        if ($this->has('name') && !$this->has('slug')) {
            $this->merge([
                'slug' => \Str::slug($this->name)
            ]);
        }

        // Set default values
        if (!$this->has('is_active')) {
            $this->merge(['is_active' => true]);
        }

        if (!$this->has('sort_order')) {
            $this->merge(['sort_order' => 0]);
        }
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Additional validation logic after basic rules pass
        if ($this->parent_id && $this->parent_id == $this->id) {
            $this->validator->errors()->add('parent_id', 'A category cannot be its own parent.');
        }
    }
}
