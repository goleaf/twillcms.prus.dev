<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Models\Post;
use App\Observers\PostObserver;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure rate limiters
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Simple routing configuration
        Route::pattern('id', '[0-9]+');

        // API route model binding
        Route::bind('post', function ($value) {
            return Post::where('id', $value)->orWhere('slug', $value)->firstOrFail();
        });

        Route::bind('category', function ($value) {
            return Category::where('id', $value)->orWhere('slug', $value)->firstOrFail();
        });

        Post::observe(PostObserver::class);
    }
}
