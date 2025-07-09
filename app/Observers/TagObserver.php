<?php

namespace App\Observers;

use App\Models\Tag;
use Illuminate\Support\Facades\Cache;

class TagObserver
{
    /**
     * Handle the Tag "created" event.
     */
    public function created(Tag $tag): void
    {
        $this->clearRelatedCaches();
    }

    /**
     * Handle the Tag "updated" event.
     */
    public function updated(Tag $tag): void
    {
        $this->clearRelatedCaches();
    }

    /**
     * Handle the Tag "deleted" event.
     */
    public function deleted(Tag $tag): void
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