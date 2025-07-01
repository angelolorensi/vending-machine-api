<?php

use App\Http\Controllers\Api\ClassificationController;
use Illuminate\Support\Facades\Route;

Route::get('classifications', [ClassificationController::class, 'index']);
Route::post('classifications', [ClassificationController::class, 'store']);
Route::get('classifications/{id}', [ClassificationController::class, 'show']);
Route::put('classifications/{id}', [ClassificationController::class, 'update']);
Route::patch('classifications/{id}', [ClassificationController::class, 'update']);
Route::delete('classifications/{id}', [ClassificationController::class, 'destroy']);
