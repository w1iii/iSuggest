<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubmitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    //Auth Controllers
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [ProfileController::class, 'show']);

    Route::middleware('role:Administrator')->group(function () {
        // Admin routes
    });

    Route::middleware('role:Employee')->group(function () {
        // Employee routes
    });

    //Suggestions Controller
    Route::get('/suggestions', [GetSuggestionsController::class, 'get']);
    Route::post('/suggestions', [SubmitController::class, 'store']);
});
