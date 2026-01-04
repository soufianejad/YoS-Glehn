<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdultAccess;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'reader',
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }

    public function showAdultInvitation(string $token)
    {

        $invitation = AdultAccess::where('access_token', $token)->firstOrFail();

        if (! $invitation->canUse()) {
            return redirect()->route('register')->with('error', 'Invalid or expired invitation token.');
        }

        return view('adult.invitation', compact('token', 'invitation'));
    }

    public function registerAdult(Request $request, string $token)
    {
        $invitation = AdultAccess::where('access_token', $token)->firstOrFail();

        if (! $invitation->canUse()) {
            return redirect()->route('register')->with('error', 'Invalid or expired invitation token.');
        }

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'adult_reader',
        ]);

        $invitation->user_id = $user->id;
        $invitation->markAsUsed();

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Adult account registered successfully!');
    }
}
