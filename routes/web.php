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
    
    // Articles Management
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
    
    // Tags Management
    Route::get('/tags', [AdminController::class, 'tags'])->name('tags.index');
    Route::get('/tags/create', [AdminController::class, 'createTag'])->name('tags.create');
    Route::post('/tags', [AdminController::class, 'storeTag'])->name('tags.store');
    Route::get('/tags/{tag}', [AdminController::class, 'showTag'])->name('tags.show');
    Route::get('/tags/{tag}/edit', [AdminController::class, 'editTag'])->name('tags.edit');
    Route::put('/tags/{tag}', [AdminController::class, 'updateTag'])->name('tags.update');
    Route::delete('/tags/{tag}', [AdminController::class, 'destroyTag'])->name('tags.destroy');
    Route::post('/tags/bulk-action', [AdminController::class, 'bulkActionTags'])->name('tags.bulk-action');
    
    // Statistics and Analytics
    Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
});

