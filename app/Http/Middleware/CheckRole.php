<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated and has at least one of the required roles
        if ($request->user() && $request->user()->hasAnyRole($roles)) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
