<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Services\NotificationService;

class DashboardController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', __('You are not associated with a school.'));
        }

        // Key Metrics
        $totalStudents = $school->students()->count();
        $totalClasses = $school->classes()->count();
        $totalTeachers = $school->teachers()->count();
        $totalBookAssignments = $school->bookAssignments()->count();

        // Chart Data: Student Growth last 6 months
        $studentsByMonth = $school->students()
            ->selectRaw(config('database.default') === 'sqlite' ? "strftime('" . '%Y-%m' . "', created_at) as month, COUNT(*) as count" : "DATE_FORMAT(created_at, '" . '%Y-%m' . "') as month, COUNT(*) as count")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->pluck('count', 'month');

        $chartLabels = collect();
        $chartData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $chartLabels->push($month->format('M'));
            $chartData->push($studentsByMonth->get($monthKey, 0));
        }

        $studentGrowthChart = [
            'labels' => $chartLabels,
            'data' => $chartData,
        ];

        // Recent Activity Feed
        $recentStudents = $school->students()->latest()->take(3)->get()->map(function ($student) {
            return ['type' => 'new_student', 'data' => $student, 'timestamp' => $student->created_at];
        });
        $recentClasses = $school->classes()->latest()->take(3)->get()->map(function ($class) {
            return ['type' => 'new_class', 'data' => $class, 'timestamp' => $class->created_at];
        });
        
        $recentActivity = $recentStudents->merge($recentClasses)->sortByDesc('timestamp')->take(5);

        // Recent Announcements
        $recentAnnouncements = $school->announcements()->latest()->take(3)->get();

        return view('school.dashboard.index', compact(
            'school', 
            'totalStudents', 
            'totalClasses',
            'totalTeachers',
            'totalBookAssignments',
            'studentGrowthChart',
            'recentActivity',
            'recentAnnouncements'
        ));
    }

    public function statistics()
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', __('You are not associated with a school.'));
        }

        $studentsByMonth = $school->students()
            ->selectRaw(config('database.default') === 'sqlite' ? "strftime('" . '%Y-%m' . "', created_at) as month, COUNT(*) as count" : "DATE_FORMAT(created_at, '" . '%Y-%m' . "') as month, COUNT(*) as count")
            ->where('role', 'student')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $bookAssignmentsByMonth = $school->bookAssignments()
            ->selectRaw(config('database.default') === 'sqlite' ? "strftime('" . '%Y-%m' . "', created_at) as month, COUNT(*) as count" : "DATE_FORMAT(created_at, '" . '%Y-%m' . "') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('school.dashboard.statistics', compact('school', 'studentsByMonth', 'bookAssignmentsByMonth'));
    }

    public function progressReport()
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', __('You are not associated with a school.'));
        }

        $students = $school->students()->with('readingProgress', __('audioProgress'), 'quizAttempts')->paginate(10);

        return view('school.dashboard.progress-report', compact('school', 'students'));
    }

}
