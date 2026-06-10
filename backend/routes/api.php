<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UpdateProfileController;
use App\Http\Controllers\Admin\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuggestionsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SuggestionController;

Route::post('/v1/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
Route::post('/v1/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Auth Controllers
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [ProfileController::class, 'show']);
    Route::post('/profile/update', [UpdateProfileController::class, 'update']);

    // Admin Specific Routes
    Route::middleware('role:Administrator')->prefix('admin')->group(function () {
        Route::get('/employees', [EmployeeController::class, 'index']);
        Route::post('/employees', [EmployeeController::class, 'store']);
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
        Route::get('/dashboard/activity', [DashboardController::class, 'activity']);
        Route::get('/dashboard/categories', [DashboardController::class, 'categories']);
        Route::get('/dashboard/contributors', [DashboardController::class, 'contributors']);
        Route::get('/dashboard/top-ideas', [DashboardController::class, 'topIdeas']);
        Route::get('/dashboard/trends', [DashboardController::class, 'trendsByPeriod']);
        Route::get('/dashboard/report', [DashboardController::class, 'downloadReport']);
        Route::get('/suggestions/statuses', [SuggestionController::class, 'statuses']);
        Route::get('/suggestions', [SuggestionController::class, 'index']);
        Route::patch('/suggestions/{id}/status', [SuggestionController::class, 'updateStatus']);
        Route::delete('/suggestions/{id}', [SuggestionController::class, 'destroy']);
    });

    // Suggestion Routes
    Route::get('/suggestions/stats', [SuggestionsController::class, 'stats']);
    Route::get('/suggestions/user-stats', [SuggestionsController::class, 'userStats']);

    Route::middleware('role:Employee')->group(function () {
        Route::get('/suggestions', [SuggestionsController::class, 'index']);
        Route::get('/suggestions/{id}', [SuggestionsController::class, 'show']);
        Route::post('/suggestions', [SuggestionsController::class, 'store']);
        Route::put('/suggestions/{id}', [SuggestionsController::class, 'update']);
        Route::delete('/suggestions/{id}', [SuggestionsController::class, 'destroy']);
    });
});
