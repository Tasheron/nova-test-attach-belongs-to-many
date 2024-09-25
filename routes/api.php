<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('product')->group(function() {
    Route::post('/find', [ProductController::class, 'find']);
    Route::get('/{product}', [ProductController::class, 'show']);
    Route::put('/{product}/category/{category}', [ProductController::class, 'attach']);
    Route::delete('/{product}/category/{category}', [ProductController::class, 'detach']);
    Route::post('/{product}/changeIndex/{category}', [ProductController::class, 'changeIndex']);
    Route::post('/{product}/updatePivot/{category}', [ProductController::class, 'updatePivot']);
});

Route::prefix('category')->group(function() {
    Route::post('/find', [CategoryController::class, 'find']);
    Route::get('/{category}', [CategoryController::class, 'show']);
    Route::put('/{category}/product/{product}', [CategoryController::class, 'attach']);
    Route::delete('/{category}/product/{product}', [CategoryController::class, 'detach']);
    Route::post('/{category}/changeIndex/{product}', [CategoryController::class, 'changeIndex']);
    Route::post('/{category}/updatePivot/{product}', [CategoryController::class, 'updatePivot']);
});
