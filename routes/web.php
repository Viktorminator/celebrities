<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CelebrityController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PhotoAnalysisController;

// Web routes
Route::get('/', [CelebrityController::class, 'home'])->name('home');
Route::resource('celebrities', CelebrityController::class);
Route::resource('product-categories', ProductCategoryController::class);
Route::resource('products', ProductController::class);
Route::resource('events', EventController::class);

// Photo Analysis routes
Route::post('/analyze-photo', [PhotoAnalysisController::class, 'analyzePhoto'])->name('analyze-photo');
Route::get('/uploads/{filename}', [PhotoAnalysisController::class, 'serveUpload'])->name('serve-upload');
Route::get('/analysis-results', function () {
    return view('analysis-results');
})->name('analysis-results');
Route::get('/celebrities/{id}', [CelebrityController::class, 'show'])->name('celebrity.show');
// API routes
Route::apiResource('api/celebrities', CelebrityController::class);
Route::apiResource('api/product-categories', ProductCategoryController::class);
Route::apiResource('api/products', ProductController::class);
Route::apiResource('api/events', EventController::class);
