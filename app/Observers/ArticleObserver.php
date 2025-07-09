<?php

namespace App\Observers;

use App\Models\Article;
use Illuminate\Support\Facades\Cache;

class ArticleObserver
{
    /**
     * Handle the Article "created" event.
     */
    public function created(Article $article): void
    {
        $this->clearRelatedCaches();
    }

    /**
     * Handle the Article "updated" event.
     */
    public function updated(Article $article): void
    {
        $this->clearRelatedCaches();
    }

    /**
     * Handle the Article "deleted" event.
     */
    public function deleted(Article $article): void
    {
        $this->clearRelatedCaches();
    }

    /**
     * Clear all related caches
     */
    private function clearRelatedCaches(): void
    {
        // Clear all cache without using tags since file cache doesn't support tagging
        Cache::flush();
    }
} 