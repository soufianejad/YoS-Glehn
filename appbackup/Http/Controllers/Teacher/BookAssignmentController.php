<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Book;
use App\Models\User;
use App\Models\Classe;
use Illuminate\Http\Request;
use App\Models\BookAssignment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BookAssignmentController extends Controller
{
    /**
     * Show the form for creating a new book assignment.
     *
     * @param  \App\Models\Classe  $class
     * @return \Illuminate\View\View
     */
    public function create(Classe $class)
    {
        if ($class->teacher_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        $class->load('students');
        
        // For now, we assume the school has access to all books.
        // A more complex logic could filter books based on school subscriptions.
        $books = Book::orderBy('title')->get();

        return view('teacher.assignments.create', compact('class', 'books'));
    }

    /**
     * Store a newly created book assignment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Classe  $class
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Classe $class)
    {
        if ($class->teacher_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        $request->validate([
            'book_id' => 'required|exists:books,id',
            // student_ids is kept for validation consistency with the form, but will be ignored.
            'student_ids' => 'sometimes|array',
            'student_ids.*' => 'exists:users,id', 
        ]);

        $book = Book::findOrFail($request->input('book_id'));

        // Create a single assignment for the entire class, as per the database schema.
        BookAssignment::updateOrCreate(
            [
                'book_id' => $book->id,
                'class_id' => $class->id, // Correct column name
            ],
            [
                'school_id' => $class->school_id,
                'assigned_at' => now(),
            ]
        );

        return redirect()->route('teacher.dashboard')
                         ->with('success', "Le livre '{$book->title}' a été assigné à la classe '{$class->name}' avec succès.");
    }
}
