<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            abort(401, 'Unauthorized');
        }

        $userRole = auth()->user()->role ?? 'user';

        // Check if user's role matches any of the allowed roles
        if (!in_array($userRole, $roles, true)) {
            abort(403, 'Access denied. Required role: ' . implode(' or ', $roles));
        }

        return $next($request);
    }
}
