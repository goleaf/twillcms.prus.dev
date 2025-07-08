<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Article;
use App\Models\Tag;
use App\Observers\ArticleObserver;
use App\Observers\TagObserver;

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

        // Use Bootstrap pagination views
        Paginator::useBootstrapFive();

        // Register model observers for caching and maintenance
        Article::observe(ArticleObserver::class);
        Tag::observe(TagObserver::class);

        if (file_exists(app_path('helpers.php'))) {
            require_once app_path('helpers.php');
        }
    }
}
