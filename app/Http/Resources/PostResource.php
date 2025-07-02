<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $this->content,
            'published' => $this->published,
            'published_at' => $this->published_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            // SEO and meta data
            'meta' => [
                'title' => $this->title,
                'description' => $this->description,
                'keywords' => $this->tags ?? '',
                'canonical_url' => url("/blog/{$this->slug}"),
                'api_url' => url("/api/v1/posts/{$this->slug}"),
                'og_image' => $this->image_url ?? null,
            ],

            // Categories relationship
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),

            // Translation data
            'translations' => $this->when($request->has('include_translations'), function () {
                return $this->translations->mapWithKeys(function ($translation) {
                    return [
                        $translation->locale => [
                            'title' => $translation->title,
                            'description' => $translation->description,
                            'content' => $translation->content,
                            'slug' => $translation->slug,
                        ],
                    ];
                });
            }),

            // Images (basic implementation)
            'images' => $this->when($this->image_url, function () {
                return [[
                    'url' => $this->image_url,
                    'alt' => $this->title,
                ]];
            }),

            // Reading time estimation
            'reading_time' => $this->when($this->content, function () {
                $wordCount = str_word_count(strip_tags($this->content));

                return max(1, round($wordCount / 200)); // Assuming 200 words per minute
            }),

            // Related posts (if loaded)
            'related_posts' => PostSummaryResource::collection($this->whenLoaded('relatedPosts')),
        ];
    }
}
