<?php

use App\Http\Controllers\Api\MachineController;
use Illuminate\Support\Facades\Route;

Route::get('machines', [MachineController::class, 'index']);
Route::post('machines', [MachineController::class, 'store']);
Route::get('machines/{id}', [MachineController::class, 'show']);
Route::put('machines/{id}', [MachineController::class, 'update']);
Route::patch('machines/{id}', [MachineController::class, 'update']);
Route::delete('machines/{id}', [MachineController::class, 'destroy']);
