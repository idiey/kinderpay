<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        if ($user->role === 'super_admin' || $user->hasRole('super_admin')) {
            return $next($request);
        }

        if (!$user->kindergarten_id) {
            abort(403, 'Unauthorized access. Tenant context missing.');
        }

        return $next($request);
    }
}
