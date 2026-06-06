# Rate Limiting Implementation

## Overview

Add API rate limiting to protect endpoints from abuse. Uses Laravel's built-in `RateLimiter` facade and `throttle` middleware (backed by `symfony/rate-limiter`, already a transitive dependency).

---

## Step 1: Define Named Limiters

Add to `AppServiceProvider::boot()` in `backend/app/Providers/AppServiceProvider.php`:

```php
<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('auth', fn (Request $request) =>
            Limit::perMinute(5)->by($request->ip()));

        RateLimiter::for('api', fn (Request $request) =>
            Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('suggestions', fn (Request $request) =>
            Limit::perMinute(30)->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('admin', fn (Request $request) =>
            Limit::perMinute(30)->by($request->user()?->id));
    }
}
```

### Limiter Tiers

| Limiter | Key | Limit | Purpose |
|---------|-----|-------|---------|
| `auth` | IP address | 5 req/min | Login & register |
| `api` | User ID (or IP if guest) | 60 req/min | General authenticated routes |
| `suggestions` | User ID | 30 req/min | Suggestions CRUD |
| `admin` | User ID | 30 req/min | Admin endpoints |

---

## Step 2: Apply Middleware to Routes

Update `backend/routes/api.php`:

```php
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\SubmitController;
use App\Http\Controllers\UpdateSuggestionController;
use App\Http\Controllers\DeleteSuggestionController;
use App\Http\Controllers\GetSuggestionsController;
use App\Http\Controllers\SuggestionsController;
use Illuminate\Support\Facades\Route;

// Auth — rate limit by IP
Route::post('/v1/register', [AuthController::class, 'register'])
    ->middleware('throttle:auth');
Route::post('/v1/login', [AuthController::class, 'login'])
    ->middleware('throttle:auth');

// General authenticated routes — 60 req/min
Route::middleware(['auth:sanctum', 'throttle:api'])
    ->prefix('v1')
    ->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [ProfileController::class, 'show']);
        Route::patch('/user', [ProfileController::class, 'update']);
    });

// Suggestions — 30 req/min
Route::middleware(['auth:sanctum', 'role:Employee', 'throttle:suggestions'])
    ->prefix('v1')
    ->group(function () {
        Route::get('/suggestions/stats', [SuggestionsController::class, 'stats']);
        Route::get('/suggestions/user-stats', [SuggestionsController::class, 'userStats']);
        Route::get('/suggestions', [GetSuggestionsController::class, 'get']);
        Route::post('/suggestions', [SubmitController::class, 'store']);
        Route::put('/suggestions/{id}', [UpdateSuggestionController::class, 'update']);
        Route::delete('/suggestions/{id}', [DeleteSuggestionController::class, 'destroy']);
    });

// Admin — 30 req/min
Route::middleware(['auth:sanctum', 'role:Administrator', 'throttle:admin'])
    ->prefix('v1/admin')
    ->group(function () {
        Route::post('/employees', [EmployeeController::class, 'store']);
    });
```

**Note:** The existing `throttle:6,1` on the login route is replaced with `throttle:auth` (5 req/min, slightly more conservative).

---

## Step 3: Verify 429 JSON Responses

The exception handler in `backend/bootstrap/app.php` already renders JSON for `api/*` routes:

```php
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->shouldRenderJsonWhen(
        fn (Request $request) => $request->is('api/*'),
    );
})
```

This means 429 (Too Many Requests) responses will automatically return JSON. No additional work needed.

---

## Summary

**Files to modify:**
- `backend/app/Providers/AppServiceProvider.php` — add `RateLimiter::for()` definitions
- `backend/routes/api.php` — replace inline `throttle:6,1` with named limiters on all groups

**No new files, no new dependencies.**
