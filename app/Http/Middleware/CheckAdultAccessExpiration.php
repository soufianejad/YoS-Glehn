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

        if ($user && $user->isAdultReader() && ($user->adultAccess && $user->adultAccess->isExpired())) {
            Auth::logout();
            return redirect()->route('login')->with('error', __('Votre accès a expiré. Veuillez contacter l\'administrateur.'));
        }

        return $next($request);
    }
}
