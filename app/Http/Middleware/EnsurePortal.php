<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortal
{
    /**
     * Restrict a route group to users whose roles belong to the given portal.
     *
     * Usage: ->middleware('portal:student') or 'portal:instructor'
     */
    public function handle(Request $request, Closure $next, string $portal): Response
    {
        $user = $request->user();

        $allowed = match ($portal) {
            'student' => $user?->isStudent(),
            'instructor' => $user?->isInstructor(),
            'admin' => $user?->portal() === 'admin',
            default => false,
        };

        if (! $allowed) {
            abort(403, 'This area is not available for your account.');
        }

        return $next($request);
    }
}
