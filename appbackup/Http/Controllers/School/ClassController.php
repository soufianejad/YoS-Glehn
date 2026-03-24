<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', 'You are not associated with a school.');
        }

        $search = $request->input('search');

        $classes = $school->classes()->with('teacher')->withCount('students');

        if ($search) {
            $classes->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('level', 'like', '%'.$search.'%');
            });
        }

        $classes = $classes->paginate(10);
        return view('school.classes.index', compact('school', 'classes', 'search'));
    }

    public function create()
    {
        $school = auth()->user()->school;
        $teachers = User::where('school_id', $school->id)->where('role', 'teacher')->get();
        return view('school.classes.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', 'You are not associated with a school.');
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('classes')->where('school_id', $school->id),
            ],
            'description' => 'nullable|string',
            'level' => 'required|string|max:255',
            'teacher_id' => ['nullable', 'exists:users,id', Rule::in(User::where('school_id', $school->id)->where('role', 'teacher')->pluck('id'))],
        ]);

        $class = $school->classes()->create([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name),
            'description' => $request->description,
            'level' => $request->level,
            'is_active' => true,
            'teacher_id' => $request->teacher_id,
        ]);

        return redirect()->route('school.classes.index')->with('success', 'Class created successfully.');
    }

    public function show(ClassModel $class)
    {
        $school = auth()->user()->school;
        // Ensure the class belongs to this school
        if ($class->school_id !== $school->id) {
            abort(403);
        }
        $class->load(['students' => function ($query) {
            $query->with('readingProgress', 'audioProgress', 'quizAttempts');
        }]);

        return view('school.classes.show', compact('class'));
    }

    public function edit(ClassModel $class)
    {
        $school = auth()->user()->school;
        // Ensure the class belongs to this school
        if ($class->school_id !== $school->id) {
            abort(403);
        }

        $class->load('teacher');
        $teachers = User::where('school_id', $school->id)->where('role', 'teacher')->get();

        return view('school.classes.edit', compact('class', 'teachers'));
    }

    public function update(Request $request, ClassModel $class)
    {
        $school = auth()->user()->school;
        // Ensure the class belongs to this school
        if ($class->school_id !== $school->id) {
            abort(403);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('classes')->where('school_id', $school->id)->ignore($class->id),
            ],
            'description' => 'nullable|string',
            'level' => 'required|string|max:255',
            'is_active' => 'boolean',
            'teacher_id' => ['nullable', 'exists:users,id', Rule::in(User::where('school_id', $school->id)->where('role', 'teacher')->pluck('id'))],
        ]);

        $class->update($request->all());

        return redirect()->route('school.classes.show', $class)->with('success', 'Class updated successfully.');
    }

    public function destroy(ClassModel $class)
    {
        $school = auth()->user()->school;
        // Ensure the class belongs to this school
        if ($class->school_id !== $school->id) {
            abort(403);
        }

        $class->delete();

        return redirect()->route('school.classes.index')->with('success', 'Class deleted successfully.');
    }

    public function addStudentsForm(ClassModel $class)
    {
        $school = auth()->user()->school;
        // Ensure the class belongs to this school
        if ($class->school_id !== $school->id) {
            abort(403);
        }
        // You might want to fetch available students here to pass to the view
        $availableStudents = User::where('school_id', $school->id)
            ->where('role', 'student')
            ->whereDoesntHave('classes', function ($query) use ($class) {
                $query->where('class_id', $class->id);
            })
            ->get();

        return view('school.classes.add-students', compact('class', 'availableStudents'));
    }

    public function addStudents(Request $request, ClassModel $class)
    {
        $school = auth()->user()->school;
        // Ensure the class belongs to this school
        if ($class->school_id !== $school->id) {
            abort(403);
        }

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => ['required', Rule::exists('users', 'id')->where('school_id', $school->id)->where('role', 'student')],
        ]);

        $students = User::whereIn('id', $request->student_ids)
            ->where('school_id', $school->id)
            ->get();

        foreach ($students as $student) {
            $class->students()->syncWithoutDetaching([$student->id => ['enrolled_at' => now()]]);
        }

        return back()->with('success', 'Students added to class successfully.');
    }

    public function removeStudent(ClassModel $class, User $student)
    {
        $school = auth()->user()->school;
        // Ensure the class belongs to this school and student belongs to this school
        if ($class->school_id !== $school->id || $student->school_id !== $school->id) {
            abort(403);
        }

        $class->students()->detach($student->id);

        return back()->with('success', 'Student removed from class successfully.');
    }
}
