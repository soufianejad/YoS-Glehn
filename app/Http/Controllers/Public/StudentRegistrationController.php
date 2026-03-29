<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class StudentRegistrationController extends Controller
{
    /**
     * Show the student registration form based on the access code.
     */
    public function showRegistrationForm(Request $request)
    {
        $code = $request->query('code');

        if (!$code) {
            return redirect()->route('home')->with('error', __('Code d\'accès manquant.'));
        }

        $school = School::where('access_code', $code)->first();

        if (!$school) {
            return redirect()->route('home')->with('error', __('Code d\'accès invalide.'));
        }

        return view('public.student_register', compact('school', 'code'));
    }

    /**
     * Handle the student registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'access_code' => 'required|string|exists:schools,access_code',
        ]);

        $school = School::where('access_code', $request->access_code)->firstOrFail();

        // Optionally, check if the school has reached its student limit
        if ($school->hasReachedStudentLimit()) {
            return back()->with('error', __('L\'école a atteint sa limite d\'élèves.'));
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'school_id' => $school->id,
            'status' => 'active', // or 'pending' if you want school admin approval
        ]);

        // Update school's current_students count
        $school->increment('current_students');

        Auth::login($user);

        return redirect()->route('student.dashboard')->with('success', __('Inscription réussie ! Bienvenue dans votre espace élève.'));
    }
}
