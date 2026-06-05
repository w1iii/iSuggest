<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\SubmitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UpdateSuggestionController;
use App\Http\Controllers\DeleteSuggestionController;

Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    //Auth Controllers
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [\App\Http\Controllers\ProfileController::class, 'show']);    
    Route::patch('/user', [\App\Http\Controllers\ProfileController::class, 'update']);

    Route::middleware('role:Administrator')->prefix('admin')->group(function () {
        Route::post('/employees', [EmployeeController::class, 'store']);
    });

    Route::middleware('role:Employee')->group(function () {
        // Employee routes
    });

    //Suggestions Controller
    Route::get('/suggestions', [GetSuggestionsController::class, 'get']);
    Route::post('/suggestions', [SubmitController::class, 'store']);

    //Manage Suggestions (update/delete)
    Route::put('/suggestions/{id}', [UpdateSuggestionController::class, 'update']);
    Route::delete('/suggestions/{id}', [DeleteSuggestionController::class, 'destroy']);
});
