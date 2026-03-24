<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté');
        }

        if (! auth()->user()->isAuthor()) {
            abort(403, 'Accès réservé aux auteurs');
        }

        return $next($request);
    }
}
