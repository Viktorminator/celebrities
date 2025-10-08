<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CelebrityController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\EventController;

// Web routes
Route::get('/', [CelebrityController::class, 'home'])->name('home');
Route::resource('celebrities', CelebrityController::class);
Route::resource('product-categories', ProductCategoryController::class);
Route::resource('products', ProductController::class);
Route::resource('events', EventController::class);

// API routes
Route::apiResource('api/celebrities', CelebrityController::class);
Route::apiResource('api/product-categories', ProductCategoryController::class);
Route::apiResource('api/products', ProductController::class);
Route::apiResource('api/events', EventController::class);
