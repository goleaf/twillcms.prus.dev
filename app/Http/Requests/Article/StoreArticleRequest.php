<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreArticleRequest extends FormRequest
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
        $this->merge([
            'slug' => Str::slug($this->title),
            'reading_time' => $this->calculateReadingTime($this->content),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:255', 'unique:articles,title'],
            'slug' => ['required', 'string', 'max:255', 'unique:articles,slug'],
            'excerpt' => ['required', 'string', 'min:10', 'max:500'],
            'content' => ['required', 'string', 'min:100'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'image_caption' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'tags' => ['required', 'array', 'min:1'],
            'tags.*' => ['exists:tags,id'],
            'reading_time' => ['required', 'integer', 'min:1'],
        ];
    }

    private function calculateReadingTime(string $content): int
    {
        $wordsPerMinute = 200;
        $numberOfWords = str_word_count(strip_tags($content));
        return max(1, ceil($numberOfWords / $wordsPerMinute));
    }
}
