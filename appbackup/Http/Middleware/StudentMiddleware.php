<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté');
        }

        if (! auth()->user()->isStudent()) {
            abort(403, 'Accès réservé aux étudiants');
        }

        // Vérifier que l'étudiant est bien lié à une école
        if (! auth()->user()->school_id) {
            return redirect()->route('home')->with('error', 'Votre compte doit être lié à une école');
        }

        return $next($request);
    }
}
