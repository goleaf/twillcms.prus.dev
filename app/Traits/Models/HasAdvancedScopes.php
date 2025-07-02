<?php

namespace App\Traits\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

trait HasAdvancedScopes
{
    /**
     * Scope for published content with optional date validation
     */
    public function scopePublished(Builder $query): Builder
    {
        $query->where('published', true);
        
        // Only add published_at condition if the column exists
        if (Schema::hasColumn($this->getTable(), 'published_at')) {
            $query->whereNotNull('published_at')
                  ->where('published_at', '<=', now());
        }
        
        return $query;
    }

    /**
     * Scope for recent content (only for models with published_at)
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        if (Schema::hasColumn($this->getTable(), 'published_at')) {
            return $query->where('published_at', '>=', now()->subDays($days));
        }
        
        // Fallback to created_at if published_at doesn't exist
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for trending content based on settings
     */
    public function scopeTrending(Builder $query): Builder
    {
        return $query->where(function ($q) {
            if (Schema::hasColumn($this->getTable(), 'settings')) {
                $q->whereJsonContains('settings->is_trending', true);
            }
            
            // Fallback for models with view_count
            if (Schema::hasColumn($this->getTable(), 'view_count')) {
                $q->orWhere('view_count', '>', 100);
            }
        });
    }

    /**
     * Scope for featured content
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where(function ($q) {
            if (Schema::hasColumn($this->getTable(), 'settings')) {
                $q->whereJsonContains('settings->is_featured', true);
            }
            
            // Fallback for models with priority
            if (Schema::hasColumn($this->getTable(), 'priority')) {
                $q->orWhere('priority', '>', 0);
            }
        });
    }

    /**
     * Scope for breaking news
     */
    public function scopeBreaking(Builder $query): Builder
    {
        if (Schema::hasColumn($this->getTable(), 'settings')) {
            return $query->whereJsonContains('settings->is_breaking', true);
        }
        
        return $query; // Return unchanged if no settings column
    }

    /**
     * Scope for popular content (high view count)
     */
    public function scopePopular(Builder $query, int $minViews = 50): Builder
    {
        if (Schema::hasColumn($this->getTable(), 'view_count')) {
            return $query->where('view_count', '>=', $minViews)
                         ->orderBy('view_count', 'desc');
        }
        
        return $query; // Return unchanged if no view_count column
    }

    /**
     * Scope for content in date range
     */
    public function scopeDateRange(Builder $query, $startDate, $endDate): Builder
    {
        $dateColumn = Schema::hasColumn($this->getTable(), 'published_at') ? 'published_at' : 'created_at';
        return $query->whereBetween($dateColumn, [$startDate, $endDate]);
    }

    /**
     * Scope for archive by year and optional month
     */
    public function scopeArchive(Builder $query, int $year, ?int $month = null): Builder
    {
        $dateColumn = Schema::hasColumn($this->getTable(), 'published_at') ? 'published_at' : 'created_at';
        
        $query->whereYear($dateColumn, $year);
        
        if ($month) {
            $query->whereMonth($dateColumn, $month);
        }
        
        return $query;
    }

    /**
     * Scope for search functionality
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            // Search in translated attributes if model uses translations
            if (method_exists($this, 'whereTranslationLike')) {
                $q->whereTranslationLike('title', "%{$term}%");
                
                if (in_array('description', $this->translatedAttributes ?? [])) {
                    $q->orWhereTranslationLike('description', "%{$term}%");
                }
                
                if (in_array('content', $this->translatedAttributes ?? [])) {
                    $q->orWhereTranslationLike('content', "%{$term}%");
                }
            } else {
                // Simple search for non-translated models
                if (Schema::hasColumn($this->getTable(), 'title')) {
                    $q->where('title', 'like', "%{$term}%");
                }
                
                if (Schema::hasColumn($this->getTable(), 'description')) {
                    $q->orWhere('description', 'like', "%{$term}%");
                }
                
                if (Schema::hasColumn($this->getTable(), 'content')) {
                    $q->orWhere('content', 'like', "%{$term}%");
                }
            }
        });
    }

    /**
     * Scope for high engagement content
     */
    public function scopeHighEngagement(Builder $query, int $minViews = 100): Builder
    {
        if (Schema::hasColumn($this->getTable(), 'view_count')) {
            return $query->where('view_count', '>=', $minViews);
        }
        
        return $query; // Return unchanged if no view_count column
    }
}
