<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use App\Models\ReadingProgress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProgressController extends Controller
{
    public function index()
    {
        $student = auth()->user();
        $totalBooksRead = $student->readingProgress()->where('completed_at', '!=', null)->count();
        $totalQuizzesTaken = $student->quizAttempts()->count();
        $averageQuizScore = $student->quizAttempts()->avg('percentage');

        return view('student.progress.index', compact('totalBooksRead', 'totalQuizzesTaken', 'averageQuizScore'));
    }

    public function reading(Request $request)
    {
        $student = auth()->user();
        $search = $request->input('search');

        $query = $student->readingProgress()->with('book');

        if ($search) {
            $query->whereHas('book', function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%');
            });
        }

        $readingProgress = $query->latest('last_read_at')->paginate(10);

        // Data for chart
        $readingActivity = ReadingProgress::where('user_id', $student->id)
            ->where('last_read_at', '>=', now()->subDays(30))
            ->orderBy('last_read_at')
            ->get()
            ->groupBy(function ($progress) {
                return $progress->last_read_at->format('Y-m-d');
            })
            ->map(function ($group) {
                return $group->sum('time_spent'); // Sum time_spent for each day
            });

        $chartLabels = $readingActivity->keys();
        $chartData = $readingActivity->values();

        return view('student.progress.reading', compact('readingProgress', 'search', 'chartLabels', 'chartData'));
    }

    public function listening(Request $request)
    {
        $student = auth()->user();
        $search = $request->input('search');

        $audioProgress = $student->audioProgress()->with('book');

        if ($search) {
            $audioProgress->whereHas('book', function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $audioProgress = $audioProgress->paginate(10);

        return view('student.progress.listening', compact('audioProgress', 'search'));
    }

    public function quizzes(Request $request)
    {
        $student = auth()->user();
        $search = $request->input('search');

        $query = $student->quizAttempts()->with('quiz.book');

        if ($search) {
            $query->whereHas('quiz.book', function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%');
            });
        }

        $quizAttempts = $query->latest()->paginate(10);

        // Data for chart
        $recentAttempts = QuizAttempt::where('user_id', $student->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at')
            ->with('quiz')
            ->get();

        $chartLabels = $recentAttempts->map(function ($attempt) {
            return $attempt->created_at->format('M d').' - '.Str::limit($attempt->quiz->title, 15);
        });
        $chartData = $recentAttempts->map(function ($attempt) {
            return $attempt->score;
        });

        return view('student.progress.quizzes', compact('quizAttempts', 'search', 'chartLabels', 'chartData'));
    }

    public function badges(Request $request)
    {
        $student = auth()->user();
        $search = $request->input('search');

        $badges = $student->badges();

        if ($search) {
            $badges->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        $badges = $badges->paginate(10);

        return view('student.progress.badges', compact('badges', 'search'));
    }

    public function leaderboard()
    {
        $leaderboard = User::where('role', 'student')
            ->withCount('readingProgress')
            ->orderByDesc('reading_progress_count')
            ->paginate(10);

        return view('student.progress.leaderboard', compact('leaderboard'));
    }
}
