<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PostSummaryResource extends JsonResource
{
    /**
     * Transform the resource into a lightweight array for listings.
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
            'published_at' => $this->published_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),

            // Essential meta for listings
            'meta' => [
                'url' => "/blog/{$this->slug}",
                'api_url' => url("/api/v1/posts/{$this->slug}"),
                'reading_time' => $this->content ? max(1, round(str_word_count(strip_tags($this->content)) / 200)) : 1,
            ],

            // Categories (lightweight)
            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'color' => $category->color ?? '#3B82F6',
                    ];
                });
            }),

            // Featured image (generated)
            'featured_image' => [
                'url' => asset("storage/images/posts/post-{$this->id}.png"),
                'alt' => $this->title,
            ],

            // Excerpt for listings (first 150 chars of content or fallback to description)
            'excerpt' => $this->content ? \Illuminate\Support\Str::limit(strip_tags($this->content), 150) : \Illuminate\Support\Str::limit(strip_tags($this->description), 150),
        ];
    }
}
