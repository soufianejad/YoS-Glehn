<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdultAccessExpiration
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Si l'utilisateur est connecté et n'est pas un admin
        if ($user && !$user->isAdmin()) {
            
            // On vérifie s'il a un accès adulte (soit par son rôle, soit par une invitation liée)
            $isAdult = $user->isAdultReader() || $user->role === 'adult';
            $adultAccess = $user->adultAccess;

            // Si c'est un adulte ou s'il a un enregistrement d'accès adulte
            if ($isAdult || $adultAccess) {
                // Si l'accès est expiré
                if ($adultAccess && $adultAccess->isExpired()) {
                    Auth::logout();
                    
                    // On invalide la session pour être sûr
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')->with('error', __('Votre accès à l\'espace adulte a expiré. Veuillez contacter l\'administrateur.'));
                }
            }
        }

        return $next($request);
    }
}
