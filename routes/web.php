<?php

use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

// Admin/Settings routes (keep existing Blade-based admin)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/', [SettingsController::class, 'update'])->name('update');
        Route::delete('/reset', [SettingsController::class, 'reset'])->name('reset');
        Route::get('/export', [SettingsController::class, 'export'])->name('export');
        Route::post('/import', [SettingsController::class, 'import'])->name('import');
    });
});

// Language switching API
Route::post('/api/language/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'lt'])) {
        session(['locale' => $locale]);

        return response()->json(['success' => true, 'locale' => $locale]);
    }

    return response()->json(['success' => false], 400);
})->name('api.language.switch');

// SPA Catch-all route - This MUST be the last route
// All frontend routes are handled by Vue Router
Route::get('/{any}', function () {
    return view('spa');
})->where('any', '^(?!admin|api).*$')->name('spa');
