<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    /**
     * Get the authenticated user's school.
     */
    private function getSchool()
    {
        return Auth::user()->school;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $school = $this->getSchool();
        $teachers = $school->teachers()->paginate(10);

        return view('school.teachers.index', compact('school', 'teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $school = $this->getSchool();

        return view('school.teachers.create', compact('school'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $school = $this->getSchool();
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'teacher',
            'school_id' => $school->id,
        ]);

        return redirect()->route('school.teachers.index')->with('success', 'Teacher added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $teacher)
    {
        $school = $this->getSchool();
        // Ensure the teacher belongs to the school
        if ($teacher->school_id !== $school->id || $teacher->role !== 'teacher') {
            abort(404);
        }

        return view('school.teachers.edit', compact('school', 'teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $teacher)
    {
        $school = $this->getSchool();
        // Ensure the teacher belongs to the school
        if ($teacher->school_id !== $school->id || $teacher->role !== 'teacher') {
            abort(403);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($teacher->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $teacher->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $teacher->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('school.teachers.index')->with('success', 'Teacher updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $teacher)
    {
        $school = $this->getSchool();
        // Ensure the teacher belongs to the school
        if ($teacher->school_id !== $school->id || $teacher->role !== 'teacher') {
            abort(403);
        }

        $teacher->delete();

        return redirect()->route('school.teachers.index')->with('success', 'Teacher deleted successfully.');
    }
}
