<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // No authentication system as per requirements
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                'min:3',
            ],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                'unique:articles,slug',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],
            'excerpt' => [
                'required',
                'string',
                'max:1000',
                'min:10',
            ],
            'content' => [
                'required',
                'string',
                'min:50',
            ],
            'image' => [
                'nullable',
                'string',
                'max:500',
            ],
            'image_caption' => [
                'nullable',
                'string',
                'max:500',
            ],
            'is_featured' => [
                'sometimes',
                'boolean',
            ],
            'is_published' => [
                'sometimes',
                'boolean',
            ],
            'status' => [
                'sometimes',
                'string',
                Rule::in(['draft', 'published', 'archived']),
            ],
            'published_at' => [
                'nullable',
                'date',
                'after_or_equal:2020-01-01',
                'before_or_equal:' . now()->addYear()->format('Y-m-d'),
            ],
            'reading_time' => [
                'sometimes',
                'integer',
                'min:1',
                'max:300', // Max 5 hours reading time
            ],
            'meta_title' => [
                'nullable',
                'string',
                'max:255',
            ],
            'meta_description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'tags' => [
                'sometimes',
                'array',
                'max:10', // Limit tags to prevent spam
            ],
            'tags.*' => [
                'exists:tags,id',
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The article title is required.',
            'title.min' => 'The article title must be at least 3 characters.',
            'title.max' => 'The article title cannot exceed 255 characters.',
            
            'slug.unique' => 'This slug is already taken. Please choose a different one.',
            'slug.regex' => 'The slug can only contain lowercase letters, numbers, and hyphens.',
            
            'excerpt.required' => 'The article excerpt is required.',
            'excerpt.min' => 'The excerpt must be at least 10 characters.',
            'excerpt.max' => 'The excerpt cannot exceed 1000 characters.',
            
            'content.required' => 'The article content is required.',
            'content.min' => 'The article content must be at least 50 characters.',
            
            'image.max' => 'The image path cannot exceed 500 characters.',
            'image_caption.max' => 'The image caption cannot exceed 500 characters.',
            
            'published_at.after_or_equal' => 'The publication date cannot be before 2020.',
            'published_at.before_or_equal' => 'The publication date cannot be more than a year in the future.',
            
            'reading_time.min' => 'Reading time must be at least 1 minute.',
            'reading_time.max' => 'Reading time cannot exceed 300 minutes.',
            
            'meta_title.max' => 'The meta title cannot exceed 255 characters.',
            'meta_description.max' => 'The meta description cannot exceed 500 characters.',
            
            'tags.max' => 'You can select a maximum of 10 tags.',
            'tags.*.exists' => 'One or more selected tags do not exist.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'article title',
            'excerpt' => 'article excerpt',
            'content' => 'article content',
            'is_featured' => 'featured status',
            'is_published' => 'published status',
            'published_at' => 'publication date',
            'reading_time' => 'reading time',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
            'image_caption' => 'image caption',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Auto-generate slug if not provided
            if (!$this->filled('slug') && $this->filled('title')) {
                $this->merge([
                    'slug' => \Illuminate\Support\Str::slug($this->title)
                ]);
            }

            // Set published_at if publishing but no date provided
            if ($this->boolean('is_published') && !$this->filled('published_at')) {
                $this->merge([
                    'published_at' => now()
                ]);
            }

            // Set status based on is_published
            if ($this->has('is_published')) {
                $this->merge([
                    'status' => $this->boolean('is_published') ? 'published' : 'draft'
                ]);
            }
        });
    }
} 