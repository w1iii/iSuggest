<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [ProfileController::class, 'show']);    
    Route::patch('/user', [ProfileController::class, 'update']);

    Route::middleware('role:Administrator')->prefix('admin')->group(function () {
        Route::post('/employees', [EmployeeController::class, 'store']);
    });

    Route::middleware('role:Employee')->group(function () {
        // Employee routes
    });
});
