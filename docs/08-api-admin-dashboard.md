# Phase 8: API — Admin Dashboard

## Goal
Build admin-only endpoints for user management, system settings, and analytics.

## Steps

### 8.1 AdminController Structure

```bash
php artisan make:controller Api/Admin/UserController
php artisan make:controller Api/Admin/DashboardController
php artisan make:controller Api/Admin/CategoryController
php artisan make:request Admin/UpdateUserRequest
```

### 8.2 Endpoints

| Method | URI | Auth | Description |
|--------|-----|------|-------------|
| GET | `/api/admin/dashboard` | Admin | Dashboard stats |
| GET | `/api/admin/users` | Admin | List all users |
| GET | `/api/admin/users/{user}` | Admin | Show user details |
| PUT | `/api/admin/users/{user}` | Admin | Update user (role, etc.) |
| DELETE | `/api/admin/users/{user}` | Admin | Delete user |
| GET | `/api/admin/suggestions/all` | Admin | All suggestions (with deleted) |
| DELETE | `/api/admin/suggestions/{suggestion}` | Admin | Force delete |

### 8.3 Dashboard Stats

```php
public function dashboard()
{
    return response()->json([
        'total_suggestions' => Suggestion::count(),
        'pending_suggestions' => Suggestion::where('status', 'pending')->count(),
        'total_users' => User::count(),
        'total_comments' => Comment::count(),
        'suggestions_by_status' => Suggestion::selectRaw('status, count(*) as count')
            ->groupBy('status')->pluck('count', 'status'),
        'suggestions_by_category' => Category::withCount('suggestions')
            ->get()->pluck('suggestions_count', 'name'),
        'recent_suggestions' => SuggestionResource::collection(
            Suggestion::latest()->limit(5)->get()
        ),
        'top_users' => User::withCount('suggestions')
            ->orderBy('suggestions_count', 'desc')->limit(5)->get(),
    ]);
}
```

### 8.4 User Management

```php
// UpdateUserRequest
public function rules(): array
{
    return [
        'name' => ['sometimes', 'string', 'max:255'],
        'email' => ['sometimes', 'email', Rule::unique('users')->ignore($this->route('user'))],
        'role' => ['sometimes', 'string', 'in:employee,moderator,admin'],
        'department' => ['nullable', 'string', 'max:100'],
    ];
}

public function destroy(User $user)
{
    // Prevent admin from deleting themselves
    if ($user->id === auth()->id()) {
        return response()->json(['message' => 'Cannot delete yourself'], 422);
    }

    // Transfer or orphan their suggestions?
    // Option: reassign to admin
    Suggestion::where('user_id', $user->id)->update(['user_id' => auth()->id()]);
    $user->delete();

    return response()->json(['message' => 'User deleted']);
}
```

### 8.5 Route Organization

Group admin routes under a prefix and middleware:

```php
Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard']);
    Route::apiResource('users', UserController::class);
    // Admin can manage categories too (override regular category routes)
});
```

### 8.6 Admin Middleware Route Registration

In `routes/api.php`:
```php
Route::middleware('auth:sanctum')->group(function () {
    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Api\Admin\DashboardController::class, 'dashboard']);
        Route::apiResource('users', App\Http\Controllers\Api\Admin\UserController::class);
    });

    // Moderator routes
    Route::middleware('role:moderator')->prefix('moderator')->group(function () {
        Route::get('/suggestions/pending', [App\Http\Controllers\Api\ModerateController::class, 'pending']);
        Route::put('/suggestions/{suggestion}/status', [App\Http\Controllers\Api\ModerateController::class, 'updateStatus']);
    });
});
```

## Deliverables
- [ ] Dashboard analytics endpoint
- [ ] User CRUD (admin only)
- [ ] Force-delete suggestions (admin)
- [ ] Cannot delete own admin account
- [ ] Route organization with role middleware
- [ ] Data visualisation-ready JSON structure
