<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthorRegistrationController extends Controller
{
    public function showJoinUs()
    {
        return view('auth.author-join');
    }

    public function showRegistrationForm()
    {
        return view('auth.register-author');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'g-recaptcha-response' => ['required', 'recaptcha'],
        ], [
            'g-recaptcha-response.required' => __('Veuillez confirmer que vous n\'êtes pas un robot.'),
            'g-recaptcha-response.recaptcha' => __('La vérification reCAPTCHA a échoué, veuillez réessayer.'),
        ]);

        if (session('verified_email') !== $request->email) {
            return back()->withErrors(['is_verified_email' => __('L\'adresse email n\'a pas été vérifiée ou a été modifiée après vérification.')])->withInput();
        }

        if (session('verified_phone') !== $request->phone) {
            return back()->withErrors(['is_verified_phone' => __('Le numéro de téléphone n\'a pas été vérifié ou a été modifié après vérification.')])->withInput();
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'password' => Hash::make($request->password),
            'role' => 'author',
            'is_verified' => true,
        ]);

        Auth::login($user);

        return redirect()->route('author.dashboard')->with('success', __('Votre compte auteur a été créé avec succès.'));
    }
}
