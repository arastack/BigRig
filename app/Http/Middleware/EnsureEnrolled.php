<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEnrolled
{
    /**
     * Students can only use the learning portal once their fees are paid.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->hasCompletedPayment()) {
            return redirect()->route('checkout.pay');
        }

        return $next($request);
    }
}
