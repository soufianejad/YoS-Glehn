<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Classe;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    /**
     * Display the teacher's dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        /** @var User $teacher */
        $teacher = Auth::user();

        // Eager load classes with a count of students for efficiency
        $classes = $teacher->managedClasses()->withCount('students')->get();

        // Calculate total students from the loaded classes
        $studentsCount = $classes->sum('students_count');

        // Get the latest 5 announcements for the teacher's school
        $announcements = Announcement::where('school_id', $teacher->school_id)
                                     ->latest()
                                     ->take(5)
                                     ->get();
        
        // Get the books for the school (educational space)
        $schoolBooks = Book::where('space', 'educational')
                            ->where('status', 'published')
                            ->latest()
                            ->paginate(10, ['*'], 'school_books'); // Paginate with a custom page name

        return view('teacher.dashboard', compact('teacher', 'classes', 'studentsCount', 'announcements', 'schoolBooks'));
    }
}