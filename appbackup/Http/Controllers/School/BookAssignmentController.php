<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\BookAssignment;
use Illuminate\Http\Request;

class BookAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;
        $search = $request->input('search');

        if (! $school) {
            return redirect()->route('home')->with('error', 'You are not associated with a school.');
        }

        $assignmentsQuery = $school->bookAssignments()->with('book', 'class');

        if ($search) {
            $assignmentsQuery->where(function ($query) use ($search) {
                $query->whereHas('book', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                })->orWhereHas('class', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }
        
        $assignments = $assignmentsQuery->latest()->paginate(10)->withQueryString();

        return view('school.books.assignments', compact('school', 'assignments', 'search'));
    }

    public function create()
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', 'You are not associated with a school.');
        }

        $classes = $school->classes()->get();
        $books = \App\Models\Book::where('space', 'educational')->get();

        return view('school.books.create-assignment', compact('school', 'classes', 'books'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', 'You are not associated with a school.');
        }

        $request->validate([
            'book_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('books', 'id')->where('space', 'educational'),
                \Illuminate\Validation\Rule::unique('book_assignments')->where('class_id', $request->class_id),
            ],
            'class_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('classes', 'id')->where('school_id', $school->id),
            ],
            'due_date' => 'nullable|date',
            'is_mandatory' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $assignment = $school->bookAssignments()->create([
            'book_id' => $request->book_id,
            'class_id' => $request->class_id,
            'assigned_at' => now(),
            'due_date' => $request->due_date,
            'is_mandatory' => $request->boolean('is_mandatory'),
            'notes' => $request->notes,
        ]);

        // Notify all students in the class
        $class = \App\Models\ClassModel::with('students')->find($request->class_id);
        $book = \App\Models\Book::find($request->book_id);

        if ($class && $book) {
            foreach ($class->students as $student) {
                app(\App\Services\NotificationService::class)->sendNotification(
                    $student,
                    'Nouveau livre assigné',
                    "Le livre '{$book->title}' a été assigné à votre classe '{$class->name}'.",
                    route('book.show', $book->slug),
                    'info'
                );
            }
        }

        return redirect()->route('school.books.assignments')->with('success', 'Book assigned successfully.');
    }

    public function destroy(BookAssignment $assignment)
    {
        $school = auth()->user()->school;
        // Ensure the assignment belongs to this school
        if ($assignment->school_id !== $school->id) {
            abort(403);
        }

        $assignment->delete();

        return back()->with('success', 'Book assignment removed successfully.');
    }

    public function edit(BookAssignment $assignment)
    {
        $school = auth()->user()->school;
        // Ensure the assignment belongs to this school
        if ($assignment->school_id !== $school->id) {
            abort(403);
        }

        return view('school.books.edit-assignment', compact('assignment'));
    }

    public function update(Request $request, BookAssignment $assignment)
    {
        $school = auth()->user()->school;
        // Ensure the assignment belongs to this school
        if ($assignment->school_id !== $school->id) {
            abort(403);
        }

        $request->validate([
            'due_date' => 'nullable|date',
            'is_mandatory' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $assignment->update($request->all());

        return back()->with('success', 'Book assignment updated successfully.');
    }
}
