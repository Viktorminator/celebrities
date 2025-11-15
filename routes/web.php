<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CelebrityController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PhotoAnalysisController;
use App\Http\Controllers\ProductLinkController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Web routes
Route::get('/', [CelebrityController::class, 'home'])->name('home');
Route::resource('celebrities', CelebrityController::class);
Route::resource('product-categories', ProductCategoryController::class);
Route::resource('products', ProductController::class);
Route::resource('events', EventController::class);

// Photo Analysis routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::post('/analyze-photo', [PhotoAnalysisController::class, 'analyzePhoto'])->name('analyze-photo');
});
Route::get('/uploads/{filename}', [PhotoAnalysisController::class, 'serveUpload'])->name('serve-upload');

// Analysis Results Page - with shareable URL
Route::get('/analysis-results', function () {
    return view('analysis-results');
})->name('analysis-results');

// New Photo Analysis API routes
Route::prefix('api/photo-analysis')->group(function () {
    Route::get('/{id}', [PhotoAnalysisController::class, 'getAnalysis'])->name('api.photo-analysis.show');
    Route::middleware('auth')->group(function () {
        Route::get('/', [PhotoAnalysisController::class, 'getAnalysisHistory'])->name('api.photo-analysis.index');
        Route::delete('/{id}', [PhotoAnalysisController::class, 'deleteAnalysis'])->name('api.photo-analysis.delete');
    });
});

// Product Link routes (require authentication)
Route::middleware('auth')->prefix('api/product-links')->group(function () {
    Route::post('/', [ProductLinkController::class, 'store'])->name('api.product-links.store');
    Route::put('/{id}', [ProductLinkController::class, 'update'])->name('api.product-links.update');
    Route::delete('/{id}', [ProductLinkController::class, 'destroy'])->name('api.product-links.destroy');
    Route::get('/detected-item/{detectedItemId}', [ProductLinkController::class, 'getByDetectedItem'])->name('api.product-links.by-detected-item');
});
// API routes
Route::apiResource('api/celebrities', CelebrityController::class);
Route::apiResource('api/product-categories', ProductCategoryController::class);
Route::apiResource('api/products', ProductController::class);
Route::apiResource('api/events', EventController::class);
