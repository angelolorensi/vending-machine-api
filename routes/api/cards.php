<?php

use App\Http\Controllers\Api\CardController;
use Illuminate\Support\Facades\Route;

Route::get('cards/{id}', [CardController::class, 'show']);
Route::delete('cards/{id}', [CardController::class, 'destroy']);
Route::post('cards', [CardController::class, 'store']);
Route::put('cards/{id}', [CardController::class, 'update']);
Route::patch('cards/{id}', [CardController::class, 'update']);
Route::post('cards/{cardId}/assign-to-employee/{employeeId}', [CardController::class, 'assignCardToEmployee']);
Route::post('cards/remove-from-employee/{employeeId}', [CardController::class, 'removeFromEmployee']);
