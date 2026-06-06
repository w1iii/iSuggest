<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\SubmitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UpdateSuggestionController;
use App\Http\Controllers\DeleteSuggestionController;
use App\Http\Controllers\GetSuggestionsController;
use App\Http\Controllers\SuggestionsController;

Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Auth Controllers
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [ProfileController::class, 'show']);    
    Route::patch('/user', [ProfileController::class, 'update']);

    // Admin Specific Routes
    Route::middleware('role:Administrator')->prefix('admin')->group(function () {
        Route::post('/employees', [EmployeeController::class, 'store']);
    });

    // Global Authenticated Suggestion Routes (Accessible to Admins and Employees)
    Route::get('/suggestions/stats', [SuggestionsController::class, 'stats']);

    // Protected Employee Suggestion Routes
    Route::middleware('role:Employee')->group(function () {
        Route::get('/suggestions/user-stats', [SuggestionsController::class, 'userStats']);
        Route::get('/suggestions', [GetSuggestionsController::class, 'get']);
        Route::post('/suggestions', [SubmitController::class, 'store']);

        // Manage Suggestions (update/delete)
        Route::put('/suggestions/{id}', [UpdateSuggestionController::class, 'update']);
        Route::delete('/suggestions/{id}', [DeleteSuggestionController::class, 'destroy']);
    });
});
