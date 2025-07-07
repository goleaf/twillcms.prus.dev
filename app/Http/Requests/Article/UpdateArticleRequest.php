<?php

namespace App\Http\Requests\Article;

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
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('title')) {
            $this->merge([
                'slug' => Str::slug($this->title),
            ]);
        }

        if ($this->has('content')) {
            $this->merge([
                'reading_time' => $this->calculateReadingTime($this->content),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'sometimes',
                'required',
                'string',
                'min:5',
                'max:255',
                Rule::unique('articles', 'title')->ignore($this->route('article')),
            ],
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('articles', 'slug')->ignore($this->route('article')),
            ],
            'excerpt' => ['sometimes', 'required', 'string', 'min:10', 'max:500'],
            'content' => ['sometimes', 'required', 'string', 'min:100'],
            'image' => ['sometimes', 'required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'image_caption' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'tags' => ['sometimes', 'required', 'array', 'min:1'],
            'tags.*' => ['exists:tags,id'],
            'reading_time' => ['sometimes', 'required', 'integer', 'min:1'],
        ];
    }

    private function calculateReadingTime(string $content): int
    {
        $wordsPerMinute = 200;
        $numberOfWords = str_word_count(strip_tags($content));
        return max(1, ceil($numberOfWords / $wordsPerMinute));
    }
}
