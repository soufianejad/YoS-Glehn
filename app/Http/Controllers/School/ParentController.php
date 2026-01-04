<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ParentController extends Controller
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
        $parents = $school->parents()->with('children')->paginate(10); // Eager load children

        return view('school.parents.index', compact('school', 'parents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $school = $this->getSchool();
        $students = $school->students()->whereNull('parent_id')->get(); // Only show students without a parent

        return view('school.parents.create', compact('school', 'students'));
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
            'student_ids' => 'nullable|array',
            'student_ids.*' => ['exists:users,id', Rule::in($school->students->pluck('id'))],
        ]);

        $parent = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'parent',
            'school_id' => $school->id,
        ]);

        if ($request->has('student_ids')) {
            $school->students()->whereIn('id', $request->student_ids)->update(['parent_id' => $parent->id]);
        }

        return redirect()->route('school.parents.index')->with('success', 'Parent added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $parent)
    {
        $school = $this->getSchool();
        if ($parent->school_id !== $school->id || $parent->role !== 'parent') {
            abort(404);
        }
        // Students that can be linked: those with no parent OR those already linked to THIS parent
        $students = $school->students()->where(function ($query) use ($parent) {
            $query->whereNull('parent_id')
                ->orWhere('parent_id', $parent->id);
        })->get();

        return view('school.parents.edit', compact('school', 'parent', 'students'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $parent)
    {
        $school = $this->getSchool();
        if ($parent->school_id !== $school->id || $parent->role !== 'parent') {
            abort(403);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($parent->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'student_ids' => 'nullable|array',
            'student_ids.*' => ['exists:users,id', Rule::in($school->students->pluck('id'))],
        ]);

        $parent->update($request->only('first_name', 'last_name', 'email'));

        if ($request->filled('password')) {
            $parent->update(['password' => Hash::make($request->password)]);
        }

        // Update student links
        // 1. Unlink all children currently associated with this parent
        $school->students()->where('parent_id', $parent->id)->update(['parent_id' => null]);
        // 2. Link the students selected in the form
        if ($request->has('student_ids')) {
            $school->students()->whereIn('id', $request->student_ids)->update(['parent_id' => $parent->id]);
        }

        return redirect()->route('school.parents.index')->with('success', 'Parent updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $school = $this->getSchool();
        $parent = $school->parents()->findOrFail($id);

        if ($parent->school_id !== $school->id || $parent->role !== 'parent') {
            abort(403);
        }

        // Unlink children before deleting the parent
        $school->students()->where('parent_id', $parent->id)->update(['parent_id' => null]);

        $parent->delete();

        return redirect()->route('school.parents.index')->with('success', 'Parent deleted successfully.');
    }
}
