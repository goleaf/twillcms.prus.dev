<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,archived',
            'user_id' => 'required|exists:users,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The post title is required.',
            'content.required' => 'The post content is required.',
            'featured_image.image' => 'The featured image must be an image file.',
            'featured_image.max' => 'The featured image may not be greater than 2MB.',
        ];
    }
}
