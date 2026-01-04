<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StudentRegistrationController extends Controller
{
    public function create()
    {
        return view('auth.student-register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'access_code' => 'required|string|exists:schools,access_code',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $school = School::where('access_code', $request->access_code)->first();

        if (! $school) {
            throw ValidationException::withMessages([
                'access_code' => ['The provided access code is invalid.'],
            ]);
        }

        // Check if the school has reached its student limit
        if ($school->hasReachedStudentLimit()) {
            throw ValidationException::withMessages([
                'access_code' => ['This school has reached its maximum student capacity.'],
            ]);
        }

        $student = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'school_id' => $school->id,
        ]);

        $school->increment('current_students');

        // Log the student in
        auth()->login($student);

        return redirect()->route('student.dashboard')->with('success', 'Registration successful! Welcome to '.$school->name);
    }
}
