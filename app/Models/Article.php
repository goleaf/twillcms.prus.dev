<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image',
        'image_caption',
        'is_featured',
        'is_published',
        'published_at',
        'reading_time',
        'view_count',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'reading_time' => 'integer',
        'view_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }

            if (empty($article->reading_time)) {
                $article->reading_time = static::calculateReadingTime($article->content);
            }

            if (empty($article->excerpt)) {
                $article->excerpt = Str::limit(strip_tags($article->content), 500);
            }

            if ($article->is_published && empty($article->published_at)) {
                $article->published_at = now();
            }
        });

        static::saving(function ($article) {
            if ($article->isDirty('title')) {
                $article->slug = Str::slug($article->title);
            }

            if ($article->isDirty('content')) {
                $article->reading_time = static::calculateReadingTime($article->content);
                if (empty($article->excerpt)) {
                    $article->excerpt = Str::limit(strip_tags($article->content), 500);
                }
            }

            if ($article->isDirty('is_published')) {
                $article->published_at = $article->is_published ? now() : null;
            }
        });
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)
            ->withTimestamps();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($query) use ($term) {
            $query->where('title', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%")
                ->orWhere('content', 'like', "%{$term}%")
                ->orWhereHas('tags', function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%");
                });
        });
    }

    public function scopeWithAnyTags(Builder $query, array $tagIds): Builder
    {
        return $query->whereHas('tags', function ($query) use ($tagIds) {
            $query->whereIn('tags.id', $tagIds);
        });
    }

    public function getRelated(int $limit = 3): \Illuminate\Database\Eloquent\Collection
    {
        return static::published()
            ->whereHas('tags', function ($query) {
                $query->whereIn('tags.id', $this->tags->pluck('id'));
            })
            ->where('id', '!=', $this->id)
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    public static function calculateReadingTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        $wordsPerMinute = 200;

        return (int) ceil($wordCount / $wordsPerMinute);
    }

    public function publish(): bool
    {
        return $this->update([
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    public function unpublish(): bool
    {
        return $this->update([
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    public function feature(): bool
    {
        return $this->update(['is_featured' => true]);
    }

    public function unfeature(): bool
    {
        return $this->update(['is_featured' => false]);
    }

    public function incrementViews(): bool
    {
        return $this->increment('view_count');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : 'https://via.placeholder.com/800x400';
    }

    public function getReadingTimeForHumansAttribute(): string
    {
        return $this->reading_time . ' min read';
    }

    public function getPublishedAtForHumansAttribute(): string
    {
        return $this->published_at?->diffForHumans();
    }
}
