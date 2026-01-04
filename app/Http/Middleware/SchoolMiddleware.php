<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SchoolMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté');
        }

        if (! auth()->user()->isSchool()) {
            abort(403, 'Accès réservé aux établissements scolaires');
        }

        // Vérifier que l'école a bien un profil configuré
        if (! auth()->user()->managedSchool) {
            return redirect()->route('school.setup')->with('warning', 'Veuillez compléter votre profil');
        }

        return $next($request);
    }
}
