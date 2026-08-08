<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            // Langsung kembalikan response view errors.403 dengan status code 403 secara eksplisit
            return response()->view('errors.403', [], 403);
        }

        return $next($request);
    }
}