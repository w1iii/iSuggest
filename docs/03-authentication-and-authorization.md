# Phase 3: Authentication & Authorization

## Goal
Implement Sanctum-based API authentication and role-based authorization.

## Steps

### 3.1 Install & Configure Sanctum
```bash
composer require laravel/sanctum
php artisan install:api
```

- Already configured per initial scaffolding — verify `HasApiTokens` on User model.
- Update `.env` with any frontend URL for CORS:
  ```
  FRONTEND_URL=http://localhost:5173
  SANCTUM_STATEFUL_DOMAINS=localhost:5173
  ```

### 3.2 Auth Controllers

**AuthController** (or separate Register/Login controllers)
- `POST /api/register` — validate name, email, password; create user (role: employee); return token
- `POST /api/login` — validate credentials; return Sanctum token
- `POST /api/logout` — revoke current token (auth:sanctum)
- `GET /api/user` — return authenticated user with roles (auth:sanctum)

### 3.3 Token Abilities

When issuing tokens, attach abilities matching the user's role:

```php
$token = $user->createToken('api', $user->getAbilities());
```

Define `getAbilities()` on the User model:
- **employee**: `['suggestions:create', 'suggestions:read', 'comments:create', 'votes:create']`
- **moderator**: employee abilities + `['suggestions:moderate', 'comments:moderate']`
- **admin**: `['*']`

### 3.4 Middleware

**RoleMiddleware** — check user has a minimum role:
```php
// Check user has at least 'moderator' role
Route::middleware('role:moderator')->group(...);
```

Define in `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\CheckRole::class,
    ]);
})
```

### 3.5 Ability Gates

Register gates for authorization checks:

```php
Gate::define('moderate-suggestion', function (User $user) {
    return $user->isModerator() || $user->isAdmin();
});

Gate::define('manage-users', function (User $user) {
    return $user->isAdmin();
});
```

### 3.6 Form Requests

- **RegisterRequest**: name (required, string, max:255), email (required, email, unique), password (required, string, min:8, confirmed)
- **LoginRequest**: email (required, email), password (required)

## Deliverables
- [ ] Register endpoint works (returns token + user)
- [ ] Login endpoint works (returns token + user)
- [ ] Logout revokes token
- [ ] Role middleware restricts routes
- [ ] Gates defined for authorization
- [ ] All endpoints tested with Postman/curl
- [ ] Token expiration strategy decided (default: null = never expires)
