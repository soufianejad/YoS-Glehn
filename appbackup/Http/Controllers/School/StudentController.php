<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;
        $search = $request->input('search');

        if (! $school) {
            return redirect()->route('home')->with('error', 'You are not associated with a school.');
        }

        $studentsQuery = $school->students();

        if ($search) {
            $studentsQuery->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $studentsQuery->latest()->paginate(10)->withQueryString();

        return view('school.students.index', compact('school', 'students', 'search'));
    }

    public function create()
    {
        return view('school.students.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', 'You are not associated with a school.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $student = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'school_id' => $school->id,
        ]);

        $school->increment('current_students');

        return redirect()->route('school.students.index')->with('success', 'Student added successfully.');
    }

    public function show(User $student)
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', 'You are not associated with a school.');
        }

        // Ensure the student belongs to this school
        if ($student->school_id !== $school->id) {
            abort(403);
        }

        return view('school.students.show', compact('student'));
    }

    public function edit(User $student)
    {
        $school = auth()->user()->school;
        // Ensure the student belongs to this school
        if ($student->school_id !== $school->id) {
            abort(403);
        }

        return view('school.students.edit', compact('student'));
    }

    public function update(Request $request, User $student)
    {
        $school = auth()->user()->school;
        // Ensure the student belongs to this school
        if ($student->school_id !== $school->id) {
            abort(403);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$student->id,
            'phone' => 'nullable|string|max:255',
        ]);

        $student->update($request->only('first_name', 'last_name', 'email', 'phone'));

        return redirect()->route('school.students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(User $student)
    {
        $school = auth()->user()->school;
        // Ensure the student belongs to this school
        if ($student->school_id !== $school->id) {
            abort(403);
        }

        $student->delete();
        $school->decrement('current_students');

        return redirect()->route('school.students.index')->with('success', 'Student deleted successfully.');
    }

    public function deactivate(User $student)
    {
        $school = auth()->user()->school;
        // Ensure the student belongs to this school
        if ($student->school_id !== $school->id) {
            abort(403);
        }

        $student->update(['is_active' => false]);

        return back()->with('success', 'Student deactivated successfully.');
    }

    public function activate(User $student)
    {
        $school = auth()->user()->school;
        // Ensure the student belongs to this school
        if ($student->school_id !== $school->id) {
            abort(403);
        }

        $student->update(['is_active' => true]);

        return back()->with('success', 'Student activated successfully.');
    }

    public function importCreate()
    {
        return view('school.students.import');
    }

    public function import(Request $request)
    {
        $school = auth()->user()->school;

        $request->validate([
            'students_file' => 'required|file|mimes:csv,xls,xlsx|max:10240', // Max 10MB
        ]);

        try {
            Excel::import(new StudentsImport($school), $request->file('students_file'));
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Row '.$failure->row().': '.implode(', ', $failure->errors());
            }

            return back()->with('error', 'There were some errors with your import: <br>'.implode('<br>', $errors));
        }

        return back()->with('success', 'Students imported successfully.');
    }

    /**
     * Download a CSV template for importing students.
     */
    public function downloadTemplate()
    {
        $content = "first_name,last_name,email,password\nJohn,Doe,john.doe@example.com,password123\nJane,Smith,jane.smith@example.com,password456";
        $fileName = 'students_import_template.csv';

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ]);
    }

    public function export()
    {
        $school = auth()->user()->school;
        $students = $school->students()->get();

        // Placeholder for export logic
        // In a real application, this would generate and download a CSV/Excel file
        return response('Exporting students data...')->header('Content-Type', 'text/plain');
    }
}
