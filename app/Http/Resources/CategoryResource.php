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
            'name' => $this->title,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'color' => $this->color_code ?? '#3B82F6',
            'position' => $this->position,
            'published' => $this->published,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            // Meta data for SEO
            'meta' => [
                'title' => $this->title,
                'description' => $this->description ?: "Posts in {$this->title} category",
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
                            'title' => $translation->title,
                            'description' => $translation->description,
                        ],
                    ];
                });
            }),

            // Icon if available
            'icon' => $this->icon ?? 'default-icon',
        ];
    }
}
