<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté');
        }

        if (! auth()->user()->isAdmin()) {
            abort(403, 'Accès réservé aux administrateurs');
        }

        return $next($request);
    }
}
