<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
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
        $articleId = $this->route('article')?->id ?? $this->route('id');

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('articles', 'slug')->ignore($articleId)],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'image_caption' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date', 'before_or_equal:now'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'article title',
            'slug' => 'article slug',
            'excerpt' => 'article excerpt',
            'content' => 'article content',
            'image' => 'article image',
            'image_caption' => 'image caption',
            'is_featured' => 'featured status',
            'is_published' => 'published status',
            'published_at' => 'publication date',
            'tags' => 'article tags',
            'tags.*' => 'tag',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => __('validation.article_title_required'),
            'title.max' => __('validation.article_title_max'),
            'slug.unique' => __('validation.article_slug_unique'),
            'content.required' => __('validation.article_content_required'),
            'image.image' => __('validation.article_image_image'),
            'image.mimes' => __('validation.article_image_mimes'),
            'image.max' => __('validation.article_image_max'),
            'published_at.before_or_equal' => __('validation.article_published_at_before_or_equal'),
            'tags.array' => __('validation.article_tags_array'),
            'tags.*.integer' => __('validation.article_tags_integer'),
            'tags.*.exists' => __('validation.article_tags_exists'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate slug if not provided
        if (!$this->slug && $this->title) {
            $this->merge([
                'slug' => Str::slug($this->title)
            ]);
        }

        // Convert checkbox values to boolean
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'is_published' => $this->boolean('is_published'),
        ]);

        // Set published_at if is_published is true and published_at is not set
        if ($this->boolean('is_published') && !$this->published_at) {
            $this->merge([
                'published_at' => now()
            ]);
        }
    }
} 