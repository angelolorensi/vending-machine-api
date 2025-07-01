<?php

use App\Http\Controllers\Api\CardController;
use Illuminate\Support\Facades\Route;

Route::get('cards/{id}', [CardController::class, 'show']);
Route::delete('cards/{id}', [CardController::class, 'destroy']);
