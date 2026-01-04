<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsConsumer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $consumerRoles = ['reader', 'adult_reader', 'student', 'parent', 'teacher','author'];

        if (!Auth::check() || !in_array(Auth::user()->role, $consumerRoles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
