<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdultAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté');
        }

        if (! auth()->user()->isAdultReader()) {
            abort(403, 'Accès réservé aux adultes');
        }

        return $next($request);
    }
}
