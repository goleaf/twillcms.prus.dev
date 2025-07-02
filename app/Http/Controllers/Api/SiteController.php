<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class SiteController extends Controller
{
    /**
     * Get site configuration and metadata
     */
    public function config(Request $request): JsonResponse
    {
        $cacheKey = 'api.site.config';

        $data = Cache::remember($cacheKey, 3600, function () { // 1 hour cache
            return [
                'site' => [
                    'name' => config('app.name', 'TwillCMS Blog'),
                    'description' => 'A modern blog built with TwillCMS and Vue.js',
                    'url' => config('app.url'),
                    'locale' => config('app.locale', 'en'),
                    'available_locales' => config('translatable.locales', ['en']),
                    'timezone' => config('app.timezone', 'UTC'),
                ],
                'meta' => [
                    'generator' => 'TwillCMS with Vue.js',
                    'version' => '1.0.0',
                    'api_version' => 'v1',
                ],
                'social' => [
                    'twitter' => '@twillcms',
                    'github' => 'https://github.com/area17/twill',
                ],
                'features' => [
                    'search' => true,
                    'categories' => true,
                    'archives' => true,
                    'translations' => true,
                    'rss' => true,
                ],
            ];
        });

        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=3600'); // 1 hour browser cache
    }

    /**
     * Get translations for the frontend
     */
    public function translations(Request $request, string $locale = 'en'): JsonResponse
    {
        $cacheKey = "api.translations.{$locale}";

        $data = Cache::remember($cacheKey, 3600, function () use ($locale) { // 1 hour cache
            $translationPath = resource_path("lang/{$locale}.json");

            if (File::exists($translationPath)) {
                $translations = json_decode(File::get($translationPath), true);
            } else {
                // Fallback to English if locale not found
                $fallbackPath = resource_path('lang/en.json');
                $translations = File::exists($fallbackPath)
                    ? json_decode(File::get($fallbackPath), true)
                    : [];
            }

            // Add common frontend translations
            $frontendTranslations = [
                'common' => [
                    'home' => 'Home',
                    'blog' => 'Blog',
                    'categories' => 'Categories',
                    'search' => 'Search',
                    'read_more' => 'Read More',
                    'loading' => 'Loading...',
                    'error' => 'Error',
                    'not_found' => 'Not Found',
                    'no_results' => 'No results found',
                    'try_again' => 'Try Again',
                ],
                'navigation' => [
                    'menu' => 'Menu',
                    'close' => 'Close',
                    'previous' => 'Previous',
                    'next' => 'Next',
                    'back_to_top' => 'Back to Top',
                ],
                'blog' => [
                    'latest_posts' => 'Latest Posts',
                    'popular_posts' => 'Popular Posts',
                    'related_posts' => 'Related Posts',
                    'published_on' => 'Published on',
                    'reading_time' => 'min read',
                    'in_category' => 'in',
                    'share' => 'Share',
                ],
                'search' => [
                    'placeholder' => 'Search posts...',
                    'results_for' => 'Results for',
                    'found_posts' => 'posts found',
                    'no_results_message' => 'No posts found matching your search.',
                ],
                'categories' => [
                    'all_categories' => 'All Categories',
                    'posts_in_category' => 'Posts in',
                    'no_posts' => 'No posts in this category yet.',
                ],
                'pagination' => [
                    'previous_page' => 'Previous Page',
                    'next_page' => 'Next Page',
                    'page' => 'Page',
                    'of' => 'of',
                    'showing' => 'Showing',
                    'to' => 'to',
                    'results' => 'results',
                ],
                'meta' => [
                    'page_title' => 'Page',
                    'home_title' => 'Home',
                    'blog_title' => 'Blog',
                    'category_title' => 'Category',
                    'search_title' => 'Search Results',
                    'not_found_title' => 'Page Not Found',
                ],
            ];

            return array_merge($translations, $frontendTranslations);
        });

        return response()->json([
            'locale' => $locale,
            'translations' => $data,
        ])
            ->header('Cache-Control', 'public, max-age=3600'); // 1 hour browser cache
    }

    /**
     * Get site statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $cacheKey = 'api.site.stats';

        $data = Cache::remember($cacheKey, 3600, function () { // 1 hour cache
            return [
                'posts' => [
                    'total' => Post::published()->count(),
                    'recent' => Post::published()
                        ->where('published_at', '>=', now()->subDays(30))
                        ->count(),
                ],
                'categories' => [
                    'total' => Category::published()->count(),
                    'with_posts' => Category::published()
                        ->has('posts')
                        ->count(),
                ],
                'latest_post' => Post::published()
                    ->latest('published_at')
                    ->first(['title', 'slug', 'published_at']),
            ];
        });

        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=3600'); // 1 hour browser cache
    }

    /**
     * Get archive data (years/months with post counts)
     */
    public function archives(Request $request): JsonResponse
    {
        $cacheKey = 'api.site.archives';

        $data = Cache::remember($cacheKey, 3600, function () { // 1 hour cache
            $archives = Post::published()
                ->selectRaw('YEAR(published_at) as year, MONTH(published_at) as month, COUNT(*) as count')
                ->groupByRaw('YEAR(published_at), MONTH(published_at)')
                ->orderByRaw('YEAR(published_at) DESC, MONTH(published_at) DESC')
                ->get()
                ->groupBy('year')
                ->map(function ($months, $year) {
                    return [
                        'year' => (int) $year,
                        'total' => $months->sum('count'),
                        'months' => $months->map(function ($month) use ($year) {
                            $monthName = \DateTime::createFromFormat('!m', $month->month)->format('F');

                            return [
                                'month' => (int) $month->month,
                                'name' => $monthName,
                                'count' => $month->count,
                                'url' => route('blog.archive', ['year' => $year, 'month' => $month->month]),
                            ];
                        })->values(),
                        'url' => route('blog.archive', ['year' => $year]),
                    ];
                })
                ->values();

            return $archives;
        });

        return response()->json($data)
            ->header('Cache-Control', 'public, max-age=3600'); // 1 hour browser cache
    }

    /**
     * Health check endpoint
     */
    public function health(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0',
            'environment' => app()->environment(),
        ]);
    }
}
