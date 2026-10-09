<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Allow the request when the user holds any of the given permissions.
     *
     * Usage: ->middleware('permission:read_payments') or 'permission:read_applicants|read_student_accounts'
     */
    public function handle(Request $request, Closure $next, string $permissions): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasAnyPermission(explode('|', $permissions))) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
