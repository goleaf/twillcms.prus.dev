<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\Admin\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// News Portal Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{article:slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/search', [NewsController::class, 'search'])->name('search');

// Tags Routes (unlimited categories)
Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
Route::get('/tags/{tag:slug}', [TagController::class, 'show'])->name('tags.show');

// Categories Routes
Route::get('/categories', [\App\Http\Controllers\CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [\App\Http\Controllers\CategoryController::class, 'show'])->name('categories.show');

// Static Pages
Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('pages.terms');
})->name('terms');

// Main Admin Route
Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin');

// Admin Routes (no authentication as per requirements)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Articles Resource Routes
    Route::resource('articles', AdminController::class, [
        'parameters' => ['articles' => 'article'],
        'names' => [
            'index' => 'articles.index',
            'create' => 'articles.create', 
            'store' => 'articles.store',
            'show' => 'articles.show',
            'edit' => 'articles.edit',
            'update' => 'articles.update',
            'destroy' => 'articles.destroy'
        ]
    ])->except(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
    
    // Custom Articles Management Routes
    Route::get('/articles', [AdminController::class, 'articles'])->name('articles.index');
    Route::get('/articles/create', [AdminController::class, 'createArticle'])->name('articles.create');
    Route::post('/articles', [AdminController::class, 'storeArticle'])->name('articles.store');
    Route::get('/articles/{article}', [AdminController::class, 'showArticle'])->name('articles.show');
    Route::get('/articles/{article}/edit', [AdminController::class, 'editArticle'])->name('articles.edit');
    Route::put('/articles/{article}', [AdminController::class, 'updateArticle'])->name('articles.update');
    Route::delete('/articles/{article}', [AdminController::class, 'destroyArticle'])->name('articles.destroy');
    Route::post('/articles/bulk-action', [AdminController::class, 'bulkActionArticles'])->name('articles.bulk-action');
    
    // Quick Actions
    Route::post('/articles/{article}/publish', [AdminController::class, 'publishArticle'])->name('articles.publish');
    Route::post('/articles/{article}/feature', [AdminController::class, 'featureArticle'])->name('articles.feature');
    
    // Tags Resource Routes
    Route::get('/tags', [AdminController::class, 'tags'])->name('tags.index');
    Route::get('/tags/create', [AdminController::class, 'createTag'])->name('tags.create');
    Route::post('/tags', [AdminController::class, 'storeTag'])->name('tags.store');
    Route::get('/tags/{tag}', [AdminController::class, 'showTag'])->name('tags.show');
    Route::get('/tags/{tag}/edit', [AdminController::class, 'editTag'])->name('tags.edit');
    Route::put('/tags/{tag}', [AdminController::class, 'updateTag'])->name('tags.update');
    Route::delete('/tags/{tag}', [AdminController::class, 'destroyTag'])->name('tags.destroy');
    Route::post('/tags/bulk-action', [AdminController::class, 'bulkActionTags'])->name('tags.bulk-action');
    
    // Categories Resource Routes  
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
    Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('categories.create');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::get('/categories/{category}', [AdminController::class, 'showCategory'])->name('categories.show');
    Route::get('/categories/{category}/edit', [AdminController::class, 'editCategory'])->name('categories.edit');
    Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');
    Route::post('/categories/bulk-action', [AdminController::class, 'bulkActionCategories'])->name('categories.bulk-action');
    
    // Statistics and Analytics
    Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
});

// Taxonomy routes (public)
Route::get('/topics', [App\Http\Controllers\TaxonomyController::class, 'index'])->name('taxonomies.index');
Route::get('/topics/{slug}', [App\Http\Controllers\ArticleController::class, 'byTaxonomy'])->name('taxonomies.show');
Route::get('/category/{slug}', [App\Http\Controllers\ArticleController::class, 'byCategory'])->name('categories.show');
Route::get('/tag/{slug}', [App\Http\Controllers\ArticleController::class, 'byTag'])->name('tags.show');

// API routes for taxonomy
Route::prefix('api/taxonomy')->group(function () {
    Route::get('/type/{type}', [App\Http\Controllers\TaxonomyController::class, 'apiByType'])->name('api.taxonomy.by-type');
    Route::get('/search', [App\Http\Controllers\TaxonomyController::class, 'apiSearch'])->name('api.taxonomy.search');
    Route::get('/popular', [App\Http\Controllers\TaxonomyController::class, 'popular'])->name('api.taxonomy.popular');
    Route::get('/featured', [App\Http\Controllers\TaxonomyController::class, 'featured'])->name('api.taxonomy.featured');
});

// Admin taxonomy routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/taxonomies/dashboard', [App\Http\Controllers\TaxonomyController::class, 'dashboard'])->name('taxonomies.dashboard');
    Route::get('/taxonomies/tree', [App\Http\Controllers\TaxonomyController::class, 'tree'])->name('taxonomies.tree');
    Route::post('/taxonomies/bulk-action', [App\Http\Controllers\TaxonomyController::class, 'bulkAction'])->name('taxonomies.bulk-action');
    Route::post('/taxonomies/rebuild-nested-set', [App\Http\Controllers\TaxonomyController::class, 'rebuildNestedSet'])->name('taxonomies.rebuild-nested-set');
    Route::post('/taxonomies/warm-cache', [App\Http\Controllers\TaxonomyController::class, 'warmCache'])->name('taxonomies.warm-cache');
    Route::resource('taxonomies', App\Http\Controllers\TaxonomyController::class);
});

