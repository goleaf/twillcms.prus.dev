<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\NewsletterSubscriberController;
use App\Http\Controllers\Admin\SiteSettingController;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Posts
    Route::resource('posts', PostController::class);

    // Categories
    Route::resource('categories', CategoryController::class);

    // Comments
    Route::resource('comments', CommentController::class);
    Route::post('comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
    Route::post('comments/{comment}/reject', [CommentController::class, 'reject'])->name('comments.reject');

    // Tags
    Route::resource('tags', TagController::class);

    // Newsletter Subscribers
    Route::resource('newsletter-subscribers', NewsletterSubscriberController::class);
    Route::get('newsletter-subscribers/export', [NewsletterSubscriberController::class, 'export'])->name('newsletter-subscribers.export');

    // Site Settings
    Route::resource('site-settings', SiteSettingController::class);
    Route::post('site-settings/clear-cache', [SiteSettingController::class, 'clearCache'])->name('site-settings.clear-cache');

});
