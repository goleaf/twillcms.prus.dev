<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\RepositoryInterface;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Eloquent\BaseRepository;
use App\Repositories\Eloquent\PostRepository;
use App\Repositories\Eloquent\CategoryRepository;
use App\Services\PostService;
use App\Models\Post;
use App\Models\Category;

/**
 * Repository Service Provider
 * 
 * Registers all repository interfaces and implementations in Laravel's IoC container.
 * Provides dependency injection configuration for the enterprise repository pattern.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        PostRepositoryInterface::class => PostRepository::class,
        CategoryRepositoryInterface::class => CategoryRepository::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // Register Post Repository with Model Injection
        $this->app->bind(PostRepositoryInterface::class, function ($app) {
            return new PostRepository($app->make(Post::class));
        });

        // Register Category Repository with Model Injection
        $this->app->bind(CategoryRepositoryInterface::class, function ($app) {
            return new CategoryRepository($app->make(Category::class));
        });

        // Register Post Service with Repository Dependencies
        $this->app->bind(PostService::class, function ($app) {
            return new PostService(
                $app->make(PostRepositoryInterface::class),
                $app->make(CategoryRepositoryInterface::class)
            );
        });

        // Register Repository Collection for Batch Operations
        $this->app->bind('repositories', function ($app) {
            return [
                'post' => $app->make(PostRepositoryInterface::class),
                'category' => $app->make(CategoryRepositoryInterface::class),
            ];
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Warm caches on application start if enabled
        if (config('cache.warm_on_boot', false)) {
            $this->warmRepositoryCaches();
        }

        // Register repository health checks
        $this->registerHealthChecks();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            PostRepositoryInterface::class,
            CategoryRepositoryInterface::class,
            PostService::class,
            'repositories',
        ];
    }

    /**
     * Warm up repository caches
     *
     * @return void
     */
    private function warmRepositoryCaches(): void
    {
        try {
            if ($this->app->environment('production')) {
                $postRepo = $this->app->make(PostRepositoryInterface::class);
                $categoryRepo = $this->app->make(CategoryRepositoryInterface::class);
                
                $postRepo->warmCache();
                $categoryRepo->warmCache();
            }
        } catch (\Exception $e) {
            \Log::warning('Repository cache warming failed: ' . $e->getMessage());
        }
    }

    /**
     * Register health checks for repositories
     *
     * @return void
     */
    private function registerHealthChecks(): void
    {
        // This could integrate with Laravel's health check package
        // For now, it's a placeholder for future health monitoring
    }
} 