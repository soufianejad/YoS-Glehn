<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Classe;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

class ClasseController extends Controller
{
    /**
     * Display a listing of the teacher's classes.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $classes = Auth::user()->managedClasses()->withCount('students')->get();
        return view('teacher.classes.index', compact('classes'));
    }

    /**
     * Display the specified class and its students.
     *
     * @param  \App\Models\Classe  $class
     * @return \Illuminate\View\View
     */
    public function show(Classe $class)
    {
        // Authorization: Ensure the teacher is assigned to this class or belongs to the same school.
        if ((int)$class->teacher_id !== (int)Auth::id() && (int)$class->school_id !== (int)Auth::user()->school_id) {
            abort(403, __('Accès non autorisé.'));
        }

        // Eager load students to prevent N+1 query problems in the view
        $class->load('students');

        return view('teacher.classes.show', compact('class'));
    }
}
