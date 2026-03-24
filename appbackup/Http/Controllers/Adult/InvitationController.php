<?php

namespace App\Http\Controllers\Adult;

use App\Http\Controllers\Controller;
use App\Models\AdultAccess;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InvitationController extends Controller
{
    public function showRegistrationForm($token)
    {
        $invitation = AdultAccess::where('access_token', $token)->first();

        if (! $invitation || ! $invitation->canUse()) {
            abort(404);
        }

        return view('adult.invitation', ['token' => $token, 'email' => $invitation->email]);
    }

    public function register(Request $request, $token)
    {
        $invitation = AdultAccess::where('access_token', $token)->first();

        if (! $invitation || ! $invitation->canUse()) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'adult_reader',
        ]);

        $invitation->markAsUsed();
        $invitation->user_id = $user->id;
        $invitation->save();

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('adult.dashboard');
    }
}
