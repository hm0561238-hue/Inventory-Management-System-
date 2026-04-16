<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests;

    /**
     * Check if user is admin
     */
    protected function authorize(?string $role = null)
    {
        if (!auth()->check()) {
            abort(401, 'Unauthorized');
        }

        if ($role && auth()->user()->role !== $role) {
            abort(403, 'Access denied. Required role: ' . $role);
        }

        if (!$role && auth()->user()->role !== 'admin') {
            abort(403, 'Admin access required');
        }
    }

    /**
     * Check if user is authenticated
     */
    protected function requireAuth()
    {
        if (!auth()->check()) {
            abort(401, 'Unauthorized');
        }
    }
}
