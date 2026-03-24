<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Classe;
use App\Models\QuizAttempt;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Models\BookAssignment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    /**
     * List all classes for the teacher to choose from for progress tracking.
     *
     * @return \Illuminate\View\View
     */
    public function listClasses()
    {
        $teacher = Auth::user();
        $classes = $teacher->managedClasses()->withCount('students')->get();

        return view('teacher.progress.list-classes', compact('classes'));
    }

    /**
     * Display the reading progress for all students in a class.
     *
     * @param  \App\Models\Classe  $class
     * @return \Illuminate\View\View
     */
    public function index(Classe $class)
    {
        if ($class->teacher_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        // Get the IDs of books assigned to this class
        $assignedBookIds = $class->bookAssignments()->pluck('book_id');

        // Get all students from the class and eager-load their progress for the assigned books
        $students = $class->students()
            ->with(['readingProgress' => function ($query) use ($assignedBookIds) {
                $query->whereIn('book_id', $assignedBookIds);
            }])
            ->get();
        
        // Get the assigned book models
        $assignedBooks = Book::whereIn('id', $assignedBookIds)->get();

        // Get all quiz attempts for the students in this class
        $quizAttempts = QuizAttempt::whereIn('user_id', $students->pluck('id'))
            ->with('quiz.book')
            ->latest()
            ->get()
            ->groupBy('user_id');

        return view('teacher.progress.index', compact('class', 'students', 'assignedBooks', 'quizAttempts'));
    }

    /**
     * Show the details of a specific quiz attempt for a student.
     *
     * @param  \App\Models\QuizAttempt  $attempt
     * @return \Illuminate\View\View
     */
    public function showQuizAttempt(QuizAttempt $attempt)
    {
        $teacher = Auth::user();
        $student = $attempt->user;

        // Authorization: Check if the student is in any of the teacher's classes.
        $teacherClassIds = $teacher->managedClasses()->pluck('id');
        $isMyStudent = $student->classes()->whereIn('classes.id', $teacherClassIds)->exists();

        if (!$isMyStudent) {
            abort(403, 'Vous n\'êtes pas autorisé à voir les résultats de cet élève.');
        }

        $attempt->load('quiz.questions', 'user');

        return view('teacher.progress.quiz_results', compact('attempt'));
    }
}
