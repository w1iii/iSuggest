<?php
// Ramos June 2, 2026 changed

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::middleware('role:Administrator')->group(function () {
        // Admin routes
    });

    Route::middleware('role:Employee')->group(function () {
        // Employee routes
    });
});
// Ramos June 2, 2026 changed
