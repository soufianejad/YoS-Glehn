<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Book;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\ReadingProgress;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $student = auth()->user();

        // Existing stats
        $totalBooksRead = $student->readingProgress()->where('completed_at', '!=', null)->count();
        $totalQuizzesTaken = $student->quizAttempts()->count();
        $averageQuizScore = $student->quizAttempts()->avg('percentage');

        // Recently Read Books (e.g., last 3 with progress)
        $recentlyReadBooks = ReadingProgress::where('user_id', $student->id)
            ->where('progress_percentage', '>', 0)
            ->with('book')
            ->latest('last_read_at')
            ->take(3)
            ->get();

        // Recommended Books (e.g., 3 random educational books not yet completed by student)
        $completedBookIds = $student->readingProgress()->where('completed_at', '!=', null)->pluck('book_id');
        $recommendedBooks = Book::where('space', 'educational')
            ->where('status', 'published')
            ->whereNotIn('id', $completedBookIds)
            ->inRandomOrder()
            ->take(3)
            ->get();

        // Upcoming Quizzes (e.g., active quizzes not yet attempted by student) - Eager load book relationship
        $attemptedQuizIds = $student->quizAttempts()->pluck('quiz_id');
        $upcomingQuizzes = Quiz::where('is_active', true)
            ->whereNotIn('id', $attemptedQuizIds)
            ->with('book') // Eager load the book relationship
            ->take(3)
            ->get();

        // Earned Badges (e.g., last 3 earned)
        $earnedBadges = $student->badges()->latest('pivot_earned_at')->take(3)->get();

        // Recent Quiz Attempts (e.g., last 3 attempts)
        $recentQuizAttempts = QuizAttempt::where('user_id', $student->id)
            ->with('quiz')
            ->latest()
            ->take(3)
            ->get();

        return view('student.dashboard.index', compact(
            'totalBooksRead',
            'totalQuizzesTaken',
            'averageQuizScore',
            'recentlyReadBooks',
            'recommendedBooks',
            'upcomingQuizzes',
            'earnedBadges',
            'recentQuizAttempts'
        ));
    }

    public function profile()
    {
        $student = auth()->user();

        return view('student.dashboard.profile', compact('student'));
    }

    public function updateProfile(Request $request)
    {
        $student = auth()->user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
        ]);

        $student->update($request->only('first_name', 'last_name', 'phone'));

        return back()->with('success', 'Profile updated successfully.');
    }

    public function school()
    {
        $student = auth()->user();
        $school = $student->school; // Assuming a school relationship on the User model

        if (! $school) {
            return redirect()->route('student.dashboard')->with('error', 'You are not associated with a school.');
        }

        return view('student.school.info', compact('school'));
    }

    public function classes()
    {
        $student = auth()->user();
        $classes = $student->classes()->with('bookAssignments.book', 'teacher')->paginate(10);

        return view('student.school.classes', compact('classes'));
    }

    public function announcements(Request $request)
    {
        $student = auth()->user();
        $school = $student->school;

        if (! $school) {
            return redirect()->route('student.dashboard')->with('error', 'You are not associated with a school.');
        }

        $search = $request->input('search');

        // Get announcements for the entire school
        $schoolAnnouncements = Announcement::where('school_id', $school->id)
            ->whereNull('class_id');

        // Get announcements specific to the student's classes
        $studentClassIds = $student->classes->pluck('id');
        $classAnnouncements = Announcement::where('school_id', $school->id)
            ->whereIn('class_id', $studentClassIds);

        // Combine and apply search filter
        $announcements = $schoolAnnouncements->union($classAnnouncements)->latest();

        if ($search) {
            $announcements->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('content', 'like', '%'.$search.'%');
            });
        }

        $announcements = $announcements->paginate(10);

        return view('student.school.announcements', compact('school', 'announcements', 'search'));
    }

    public function classmates(Request $request)
    {
        $student = auth()->user();
        $school = $student->school;

        if (! $school) {
            return redirect()->route('student.dashboard')->with('error', 'You are not associated with a school.');
        }

        // Check if the school allows students to view classmates
        if (! $school->students_can_view_classmates) {
            return redirect()->route('student.dashboard')->with('error', 'Your school does not allow viewing classmates.');
        }

        $search = $request->input('search');
        $classmates = collect();

        foreach ($student->classes as $class) {
            $classmates = $classmates->merge($class->students()->where('users.id', '!=', $student->id)->get());
        }

        $classmates = $classmates->unique('id');

        if ($search) {
            $classmates = $classmates->filter(function ($classmate) use ($search) {
                return Str::contains(strtolower($classmate->first_name), strtolower($search)) ||
                       Str::contains(strtolower($classmate->last_name), strtolower($search)) ||
                       Str::contains(strtolower($classmate->email), strtolower($search));
            });
        }

        $page = request()->get('page', 1);
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage;

        $classmates = new LengthAwarePaginator(
            $classmates->slice($offset, $perPage),
            $classmates->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('student.school.classmates', compact('classmates', 'search'));
    }
}
