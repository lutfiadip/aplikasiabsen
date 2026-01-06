<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && method_exists($user, 'isActive') && ! $user->isActive()) {
            auth()->logout();

            abort(403, 'Your account is deactivated.');
        }

        return $next($request);
    }
}
