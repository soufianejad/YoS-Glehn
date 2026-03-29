<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Custom logic for the adult section access
        if ($role === 'adult_reader') {
            // Admins have automatic access
            if ($user->isAdmin()) {
                return $next($request);
            }

            // Check if the user has a valid invitation/access record
            $adultAccess = \App\Models\AdultAccess::where('user_id', $user->id)->first();

            // Check for expiration if access record exists
            if ($adultAccess && $adultAccess->isExpired()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', __('Votre accès à l\'espace adulte a expiré.'));
            }

            // Grant access if they have a record OR if their role is 'adult_reader' or 'adult'
            if (($adultAccess && $adultAccess->status === 'used') || $user->role === 'adult_reader' || $user->role === 'adult') {
                return $next($request);
            }

            // If none of the conditions are met, deny access
            abort(403, __('Unauthorized. You do not have access to the adult section.'));
        }

        // Original logic for all other roles
        if ($user->role !== $role) {
            abort(403, __('Unauthorized action.'));
        }

        return $next($request);
    }
}
