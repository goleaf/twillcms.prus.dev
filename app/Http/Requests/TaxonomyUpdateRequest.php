<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaxonomyUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $taxonomyId = $this->route('taxonomy') ?? $this->route('id');
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2'
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('taxonomies')->where(function ($query) {
                    return $query->where('type', $this->input('type'));
                })->ignore($taxonomyId)
            ],
            'type' => [
                'required',
                'string',
                'in:tag,category,brand,color,size,location,genre,topic'
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:taxonomies,id',
                function ($attribute, $value, $fail) {
                    if ($value && $this->input('type') !== 'category') {
                        $fail('Parent taxonomy can only be set for category type.');
                    }
                    // Prevent setting parent to self
                    if ($value && $value == $this->route('taxonomy')) {
                        $fail('A taxonomy cannot be its own parent.');
                    }
                }
            ],
            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999'
            ],
            'meta' => [
                'nullable',
                'array'
            ],
            'meta.color' => [
                'nullable',
                'string',
                'regex:/^#[0-9a-fA-F]{6}$/'
            ],
            'meta.is_featured' => [
                'nullable',
                'boolean'
            ],
            'meta.icon' => [
                'nullable',
                'string',
                'max:100'
            ],
            'meta.title' => [
                'nullable',
                'string',
                'max:255'
            ],
            'meta.is_active' => [
                'nullable',
                'boolean'
            ],
            'meta.usage_count' => [
                'nullable',
                'integer',
                'min:0'
            ]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The taxonomy name is required.',
            'name.min' => 'The taxonomy name must be at least 2 characters.',
            'name.max' => 'The taxonomy name cannot exceed 255 characters.',
            'slug.unique' => 'This slug already exists for this taxonomy type.',
            'slug.alpha_dash' => 'The slug may only contain letters, numbers, dashes, and underscores.',
            'type.required' => 'The taxonomy type is required.',
            'type.in' => 'The selected taxonomy type is invalid.',
            'description.max' => 'The description cannot exceed 1000 characters.',
            'parent_id.exists' => 'The selected parent taxonomy does not exist.',
            'sort_order.min' => 'The sort order must be at least 0.',
            'sort_order.max' => 'The sort order cannot exceed 9999.',
            'meta.color.regex' => 'The color must be a valid hex color code (e.g., #FF0000).',
            'meta.icon.max' => 'The icon class cannot exceed 100 characters.',
            'meta.title.max' => 'The title cannot exceed 255 characters.',
            'meta.usage_count.min' => 'The usage count cannot be negative.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'taxonomy name',
            'slug' => 'taxonomy slug',
            'type' => 'taxonomy type',
            'description' => 'taxonomy description',
            'parent_id' => 'parent taxonomy',
            'sort_order' => 'sort order',
            'meta.color' => 'color',
            'meta.is_featured' => 'featured status',
            'meta.icon' => 'icon',
            'meta.title' => 'display title',
            'meta.is_active' => 'active status',
            'meta.usage_count' => 'usage count',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate slug from name if not provided
        if (!$this->filled('slug') && $this->filled('name')) {
            $this->merge([
                'slug' => \Str::slug($this->name)
            ]);
        }

        // Ensure meta is an array
        if ($this->filled('meta') && is_string($this->meta)) {
            $this->merge([
                'meta' => json_decode($this->meta, true) ?: []
            ]);
        }
    }
} 