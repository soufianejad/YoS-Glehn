<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
            return redirect()->route('home')->with('error', 'You are not associated with a school.');
        }

        // Key Metrics
        $totalStudents = $school->students()->count();
        $totalClasses = $school->classes()->count();
        $totalTeachers = $school->teachers()->count();
        $totalBookAssignments = $school->bookAssignments()->count();

        // Chart Data: Student Growth last 6 months
        $studentsByMonth = $school->students()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
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
            return redirect()->route('home')->with('error', 'You are not associated with a school.');
        }

        $studentsByMonth = $school->students()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->where('role', 'student')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $bookAssignmentsByMonth = $school->bookAssignments()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
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
            return redirect()->route('home')->with('error', 'You are not associated with a school.');
        }

        $students = $school->students()->with('readingProgress', 'audioProgress', 'quizAttempts')->paginate(10);

        return view('school.dashboard.progress-report', compact('school', 'students'));
    }

    public function settings()
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', 'You are not associated with a school.');
        }

        return view('school.settings.index', compact('school'));
    }

    public function updateSettings(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', 'You are not associated with a school.');
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('schools')->ignore($school->id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('schools')->ignore($school->id),
            ],
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'primary_color' => ['nullable', 'string', 'max:7', 'starts_with:#'],
            'students_can_view_classmates' => 'boolean',
        ]);

        $schoolData = $request->except(['logo', 'banner_image']);
        $schoolData['students_can_view_classmates'] = $request->boolean('students_can_view_classmates');

        if ($request->hasFile('logo')) {
            $uploadedLogo = $request->file('logo');
            if ($uploadedLogo && $uploadedLogo->isValid()) {
                if ($school->logo) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($school->logo);
                }
                $path = 'schools/logos';
                $name = \Illuminate\Support\Str::random(40).'.'.$uploadedLogo->getClientOriginalExtension();
                \Illuminate\Support\Facades\Storage::disk('public')->put($path.'/'.$name, file_get_contents($uploadedLogo));
                $schoolData['logo'] = $path.'/'.$name;
            }
        }

        if ($request->hasFile('banner_image')) {
            $uploadedBanner = $request->file('banner_image');
            if ($uploadedBanner && $uploadedBanner->isValid()) {
                if ($school->banner_image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($school->banner_image);
                }
                // Manual store to avoid "Path must not be empty" error
                $path = 'schools/banners';
                $name = \Illuminate\Support\Str::random(40).'.'.$uploadedBanner->getClientOriginalExtension();
                \Illuminate\Support\Facades\Storage::disk('public')->put($path.'/'.$name, file_get_contents($uploadedBanner));
                $schoolData['banner_image'] = $path.'/'.$name;
            } else {
                // Log a warning or handle the invalid file case. For now, we'll just skip storing it.
                \Log::warning('Uploaded banner_image file is not valid or could not be retrieved.', ['file_info' => $uploadedBanner]);
            }
        }

        $school->update($schoolData);

        return back()->with('success', 'School settings updated successfully.');
    }

    public function regenerateAccessCode()
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', 'You are not associated with a school.');
        }

        $school->access_code = strtoupper(Str::random(8));
        $school->save();

        return back()->with('success', 'Access code regenerated successfully.');
    }

    /**
     * Display the school's registration QR code.
     */
    public function showQrCode()
    {
        $user = auth()->user();
        $school = $user->managedSchool;

        if (! $school) {
            return redirect()->route('school.dashboard')->with('error', 'Vous n\'êtes associé à aucune école.');
        }

        // Ensure the school has an access code
        if (empty($school->access_code)) {
            $school->access_code = strtoupper(Str::random(8));
            $school->save();
        }

        $registrationUrl = route('student.register', ['code' => $school->access_code]);

        return view('school.dashboard.qrcode', compact('school', 'registrationUrl'));
    }
}
