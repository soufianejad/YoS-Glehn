<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Ensure user is a teacher
        if ($user->role !== 'teacher') {
            return redirect()->route('home')->with('error', __('Accès non autorisé.'));
        }

        // The teacher belongs to a school
        $school = $user->school;

        if (!$school) {
            return redirect()->route('home')->with('error', __('Vous n\'êtes associé à aucune école.'));
        }

        // Get classes assigned to this teacher (assumes a relation exists or we just show school classes for now if no specific assignment exists)
        // Here we just fetch classes from the school, but you might want a Many-to-Many or One-to-Many relation between Teacher and Class
        $classes = $school->classes()->withCount('students')->get();

        return view('teacher.dashboard.index', compact('school', 'classes'));
    }
}
