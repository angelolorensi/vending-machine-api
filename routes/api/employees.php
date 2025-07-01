<?php

use App\Http\Controllers\Api\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::get('employees', [EmployeeController::class, 'index']);
Route::post('employees', [EmployeeController::class, 'store']);
Route::get('employees/{id}', [EmployeeController::class, 'show']);
Route::put('employees/{id}', [EmployeeController::class, 'update']);
Route::patch('employees/{id}', [EmployeeController::class, 'update']);
Route::delete('employees/{id}', [EmployeeController::class, 'destroy']);
