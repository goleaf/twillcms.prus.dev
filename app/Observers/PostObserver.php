<?php

namespace App\Observers;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
    public function created(Post $post): void
    {
        $this->clearCache();
    }

    public function updated(Post $post): void
    {
        $this->clearCache();
    }

    public function deleted(Post $post): void
    {
        $this->clearCache();
    }

    private function clearCache(): void
    {
        Cache::forget('posts.published');
        Cache::forget('posts.recent');
        Cache::forget('posts.featured');
    }
}
