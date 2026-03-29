<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdultAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', __('Vous devez être connecté'));
        }

        $user = Auth::user();

        // Admins should always have access
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if the user is an adult reader or has the adult role
        if (! $user->isAdultReader() && $user->role !== 'adult') {
            // Check if they have an invitation/access record even if role is different
            if (! $user->adultAccess || $user->adultAccess->status !== 'used') {
                abort(403, __('Accès réservé aux adultes'));
            }
        }

        // Double check expiration here as well
        if ($user->adultAccess && $user->adultAccess->isExpired()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', __('Votre accès à l\'espace adulte a expiré.'));
        }

        return $next($request);
    }
}
