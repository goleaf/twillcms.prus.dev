<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

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
        // Simple routing configuration
        Route::pattern('id', '[0-9]+');
        
        // API route model binding
        Route::bind('post', function ($value) {
            return \App\Models\Post::where('id', $value)->orWhere('slug', $value)->firstOrFail();
        });
        
        Route::bind('category', function ($value) {
            return \App\Models\Category::where('id', $value)->orWhere('slug', $value)->firstOrFail();
        });
    }
}
