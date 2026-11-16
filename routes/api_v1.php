<?php

use Illuminate\Support\Facades\Route;

Route::get('/products/queries', [App\Http\Controllers\Api\V1\ProductController::class, 'queries']);
Route::get('/products/stats', [App\Http\Controllers\Api\V1\ProductController::class, 'stats']);
Route::apiResource('products', App\Http\Controllers\Api\V1\ProductController::class);
Route::apiResource('categories', App\Http\Controllers\Api\V1\CategoryController::class);
