<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
class SchoolManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $schoolsQuery = School::query();

        if ($search) {
            $schoolsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $schoolsQuery->where('status', $status);
        }

        $schools = $schoolsQuery->latest()->paginate(10)->withQueryString();

        return view('admin.schools.index', compact('schools', 'search', 'status'));
    }

    public function pending()
    {
        $schools = School::where('status', 'pending')->paginate(10);

        return view('admin.schools.pending', compact('schools'));
    }

    public function show(School $school)
    {
        return view('admin.schools.show', compact('school'));
    }

    public function approve(School $school)
    {
        $school->update(['status' => 'approved']);

        return back()->with('success', 'School approved successfully.');
    }

    public function reject(School $school)
    {
        $school->update(['status' => 'rejected']);

        return back()->with('success', 'School rejected.');
    }

    public function suspend(School $school)
    {
        $school->update(['status' => 'suspended']);

        return back()->with('success', 'School suspended successfully.');
    }

    public function students(School $school)
    {
        $students = $school->students()->paginate(10); // Assuming a 'students' relationship in School model

        return view('admin.schools.students', compact('school', 'students'));
    }

    public function statistics(School $school)
    {
        // Placeholder for school-specific statistics
        $totalStudents = $school->students()->count();
        $totalClasses = $school->classes()->count();
        $totalBookAssignments = $school->bookAssignments()->count();

        return view('admin.schools.statistics', compact('school', 'totalStudents', 'totalClasses', 'totalBookAssignments'));
    }
}
