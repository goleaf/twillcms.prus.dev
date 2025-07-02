<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'color' => $this->color ?? '#3B82F6',
            'position' => $this->position,
            'published' => $this->published,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            // Meta data for SEO
            'meta' => [
                'title' => $this->name,
                'description' => $this->description ?: "Posts in {$this->name} category",
                'canonical_url' => url("/categories/{$this->slug}"),
            ],

            // Posts count
            'posts_count' => $this->whenCounted('posts'),

            // Posts (when loaded)
            'posts' => PostSummaryResource::collection($this->whenLoaded('posts')),

            // Translation data
            'translations' => $this->when($request->has('include_translations'), function () {
                return $this->translations->mapWithKeys(function ($translation) {
                    return [
                        $translation->locale => [
                            'name' => $translation->name,
                            'description' => $translation->description,
                            'slug' => $translation->slug,
                        ],
                    ];
                });
            }),

            // Image/icon if available
            'image' => $this->when($this->image_url, function () {
                return [
                    'url' => $this->image_url,
                    'alt' => $this->name,
                ];
            }),
        ];
    }
}
