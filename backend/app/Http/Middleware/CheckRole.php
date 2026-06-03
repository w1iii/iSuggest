<?php
// Ramos June 2, 2026 changed

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if ($request->user() && $request->user()->role === $role) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized access'], 403);
    }
}
// Ramos June 2, 2026 changed
