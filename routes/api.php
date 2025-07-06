<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\SiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Simple test route
Route::get('/test', function () {
    return response()->json(['message' => 'API routes are working', 'time' => now()]);
});

// Health check (no rate limiting)
Route::get('/health', [SiteController::class, 'health'])->name('api.health');

// Language switching endpoint removed - only English is supported

// API v1 routes
Route::prefix('v1')->group(function () {

    // Site configuration and metadata
    Route::prefix('site')->name('api.site.')->group(function () {
        Route::get('/config', [SiteController::class, 'config'])->name('config');
        Route::get('/stats', [SiteController::class, 'stats'])->name('stats');
        Route::get('/archives', [SiteController::class, 'archives'])->name('archives');
        Route::get('/translations/{locale?}', [SiteController::class, 'translations'])->name('translations');
    });

    // Posts API
    Route::prefix('posts')->name('api.posts.')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('index');
        Route::get('/popular', [PostController::class, 'popular'])->name('popular');
        Route::get('/recent', [PostController::class, 'recent'])->name('recent');
        Route::get('/search', [PostController::class, 'search'])->name('search');
        Route::get('/archive/{year}/{month?}', [PostController::class, 'archive'])
            ->where(['year' => '[0-9]{4}', 'month' => '[0-9]{1,2}'])
            ->name('archive');
        Route::get('/{slug}', [PostController::class, 'show'])
            ->where('slug', '[a-z0-9\-]+')
            ->name('show');
    });

    // Categories API
    Route::prefix('categories')->name('api.categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/navigation', [CategoryController::class, 'navigation'])->name('navigation');
        Route::get('/popular', [CategoryController::class, 'popular'])->name('popular');
        Route::get('/{slug}', [CategoryController::class, 'show'])
            ->where('slug', '[a-z0-9\-]+')
            ->name('show');
    });

    // Pages API
    Route::prefix('pages')->name('api.pages.')->group(function () {
        Route::get('/', [PageController::class, 'index'])->name('index');
        Route::get('/static', [PageController::class, 'getStaticPages'])->name('static');
        Route::get('/{slug}', [PageController::class, 'show'])
            ->where('slug', '[a-z0-9\-]+')
            ->name('show');
    });

});

// Translation endpoints (English only)
Route::get('/site/translations/{locale}', function ($locale) {
    // Only English is supported
    if ($locale !== 'en') {
        return response()->json(['error' => 'Only English locale is supported'], 404);
    }

    $path = resource_path('lang/en.json');

    if (! file_exists($path)) {
        return response()->json(['error' => 'Translation file not found'], 404);
    }

    $translations = json_decode(file_get_contents($path), true);

    return response()->json([
        'locale' => 'en',
        'translations' => $translations,
    ]);
})->where('locale', 'en')->name('api.translations');

// Catch-all for undefined API routes
Route::fallback(function () {
    return response()->json([
        'error' => 'API endpoint not found',
        'message' => 'The requested API endpoint does not exist.',
        'available_endpoints' => [
            'GET /api/v1/site/config' => 'Site configuration',
            'GET /api/v1/posts' => 'List posts',
            'GET /api/v1/posts/{slug}' => 'Get post by slug',
            'GET /api/v1/categories' => 'List categories',
            'GET /api/v1/categories/{slug}' => 'Get category by slug',
        ],
    ], 404);
})->name('api.fallback');
