<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// SPA Routes - All frontend routes handled by Vue.js
Route::get('/', function () {
    return view('spa');
})->name('spa.home');

Route::get('/{any}', function () {
    return view('spa');
})->where('any', '^(?!admin|api).*$')->name('spa.fallback');

/*
|--------------------------------------------------------------------------
| Admin Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Posts Management
    Route::resource('posts', PostController::class);

    // Categories Management
    Route::resource('categories', CategoryController::class);
});
