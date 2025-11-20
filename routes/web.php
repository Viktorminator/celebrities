<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CelebrityController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PhotoAnalysisController;
use App\Http\Controllers\ProductLinkController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\StyleController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\StyleFavouriteController;

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Web routes
Route::get('/', [CelebrityController::class, 'home'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/categories', [CategoriesController::class, 'index'])->name('categories');
Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions');

// Public Style View Page (must come before auth routes to avoid conflicts)
Route::get('/style/{id}', [StyleController::class, 'view'])->name('style.view');

// User Styles routes (require authentication)
Route::middleware('auth')->prefix('styles')->name('styles.')->group(function () {
    Route::get('/create', [StyleController::class, 'create'])->name('create');
    Route::post('/', [StyleController::class, 'store'])->name('store');
    Route::get('/', [StyleController::class, 'index'])->name('index');
    Route::get('/{id}/edit', [StyleController::class, 'edit'])->name('edit');
//    Route::get('/{id}', [StyleController::class, 'show'])->name('show');
    Route::put('/{id}', [StyleController::class, 'update'])->name('update');
    Route::delete('/{id}', [StyleController::class, 'destroy'])->name('destroy');
});
Route::resource('celebrities', CelebrityController::class);
Route::resource('product-categories', CategoryController::class);
Route::resource('products', ProductController::class);
Route::resource('events', EventController::class);

// Photo Analysis routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::post('/analyze-photo', [PhotoAnalysisController::class, 'analyzePhoto'])->name('analyze-photo');
});
Route::get('/uploads/{filename}', [PhotoAnalysisController::class, 'serveUpload'])->name('serve-upload');

// Analysis Results Page - with shareable URL (kept for backward compatibility)
Route::get('/analysis-results', function () {
    return view('analysis-results');
})->name('analysis-results');

// New Photo Analysis API routes
Route::prefix('api/photo-analysis')->group(function () {
    Route::get('/{id}', [cardController::class, 'getAnalysis'])->name('api.photo-analysis.show');
    Route::middleware('auth')->group(function () {
        Route::get('/', [cardController::class, 'getAnalysisHistory'])->name('api.photo-analysis.index');
        Route::delete('/{id}', [cardController::class, 'deleteAnalysis'])->name('api.photo-analysis.delete');
    });
});

// Product Link routes
Route::get('/api/product-links/{id}/track', [ProductLinkController::class, 'track'])->name('api.product-links.track');
Route::middleware('auth')->prefix('api/product-links')->group(function () {
    Route::post('/', [ProductLinkController::class, 'store'])->name('api.product-links.store');
    Route::put('/{id}', [ProductLinkController::class, 'update'])->name('api.product-links.update');
    Route::delete('/{id}', [ProductLinkController::class, 'destroy'])->name('api.product-links.destroy');
    Route::get('/detected-item/{detectedItemId}', [ProductLinkController::class, 'getByDetectedItem'])->name('api.product-links.by-detected-item');
});

// Favourites routes
Route::get('/api/favourites/{productLinkId}/check', [FavouriteController::class, 'check'])->name('api.favourites.check');
Route::middleware('auth')->prefix('api/favourites')->group(function () {
    Route::post('/{productLinkId}/toggle', [FavouriteController::class, 'toggle'])->name('api.favourites.toggle');
});

// Likes routes
Route::get('/api/likes/{cardId}/check', [LikeController::class, 'check'])->name('api.likes.check');
Route::middleware('auth')->prefix('api/likes')->group(function () {
    Route::post('/{cardId}/toggle', [LikeController::class, 'toggle'])->name('api.likes.toggle');
});

// Style Favourites routes
Route::get('/favourites', [StyleFavouriteController::class, 'index'])->name('favourites.index');
Route::get('/api/style-favourites/{cardId}/check', [StyleFavouriteController::class, 'check'])->name('api.style-favourites.check');
Route::post('/api/style-favourites/{cardId}/toggle', [StyleFavouriteController::class, 'toggle'])->name('api.style-favourites.toggle');
// API routes
Route::apiResource('api/celebrities', CelebrityController::class);
Route::apiResource('api/product-categories', CategoryController::class);
Route::apiResource('api/products', ProductController::class);
Route::apiResource('api/events', EventController::class);
