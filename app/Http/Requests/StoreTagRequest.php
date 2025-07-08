<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreTagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // No authentication required as per requirements
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:tags,name'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:tags,slug'],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_featured' => ['boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'tag name',
            'slug' => 'tag slug',
            'description' => 'tag description',
            'color' => 'tag color',
            'is_featured' => 'featured status',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.tag_name_required'),
            'name.max' => __('validation.tag_name_max'),
            'name.unique' => __('validation.tag_name_unique'),
            'slug.unique' => __('validation.tag_slug_unique'),
            'color.regex' => __('validation.tag_color_regex'),
            'description.max' => __('validation.tag_description_max'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate slug if not provided
        if (!$this->slug && $this->name) {
            $this->merge([
                'slug' => Str::slug($this->name)
            ]);
        }

        // Convert checkbox values to boolean
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
        ]);

        // Set default color if not provided
        if (!$this->color) {
            $defaultColors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#F97316', '#06B6D4', '#84CC16'];
            $this->merge([
                'color' => $defaultColors[array_rand($defaultColors)]
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate slug uniqueness with a more specific check
            if ($this->slug) {
                $existingTag = \App\Models\Tag::where('slug', $this->slug)->first();
                if ($existingTag) {
                    $validator->errors()->add('slug', 'This slug is already taken.');
                }
            }
        });
    }
} 