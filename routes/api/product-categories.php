<?php

use App\Http\Controllers\Api\ProductCategoryController;
use Illuminate\Support\Facades\Route;

Route::get('product-categories', [ProductCategoryController::class, 'index']);
Route::post('product-categories', [ProductCategoryController::class, 'store']);
Route::get('product-categories/{id}', [ProductCategoryController::class, 'show']);
Route::put('product-categories/{id}', [ProductCategoryController::class, 'update']);
Route::patch('product-categories/{id}', [ProductCategoryController::class, 'update']);
Route::delete('product-categories/{id}', [ProductCategoryController::class, 'destroy']);
