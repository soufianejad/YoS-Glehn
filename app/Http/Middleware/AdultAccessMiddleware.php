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
            if (! $user->adultAccess || !in_array($user->adultAccess->status, ['used', 'pending'])) {
                abort(403, __('Accès réservé aux adultes'));
            }
        }

        // Double check expiration/revocation here as well
        if ($user->adultAccess && $user->adultAccess->isExpired()) {
            $status = $user->adultAccess->status;
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            $message = ($status === 'revoked') 
                ? __('Votre accès à l\'espace adulte a été révoqué.')
                : __('Votre accès à l\'espace adulte a expiré.');

            return redirect()->route('login')->with('error', $message);
        }

        return $next($request);
    }
}
