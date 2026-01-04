<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', 'You are not associated with a school.');
        }

        $search = $request->input('search');

        $announcements = Announcement::where('school_id', $school->id);

        if ($search) {
            $announcements->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('content', 'like', '%'.$search.'%');
            });
        }

        $announcements = $announcements->latest()->paginate(10);

        return view('school.announcements.index', compact('school', 'announcements', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', 'You are not associated with a school.');
        }

        $classes = $school->classes()->get();

        return view('school.announcements.create', compact('school', 'classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school) {
            return redirect()->route('home')->with('error', 'You are not associated with a school.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:published_at',
            'class_id' => ['nullable', 'exists:classes,id', Rule::in($school->classes->pluck('id'))],
        ]);

        $school->announcements()->create([
            'title' => $request->title,
            'content' => $request->content,
            'published_at' => $request->published_at ?? now(),
            'expires_at' => $request->expires_at,
            'class_id' => $request->class_id,
        ]);

        return redirect()->route('school.announcements.index')->with('success', 'Announcement created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school || $announcement->school_id !== $school->id) {
            abort(403, 'Unauthorized action.');
        }

        return view('school.announcements.show', compact('school', 'announcement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Announcement $announcement)
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school || $announcement->school_id !== $school->id) {
            abort(403, 'Unauthorized action.');
        }

        $classes = $school->classes()->get();
        $announcement->load('class');

        return view('school.announcements.edit', compact('school', 'announcement', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school || $announcement->school_id !== $school->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:published_at',
            'class_id' => ['nullable', 'exists:classes,id', Rule::in($school->classes->pluck('id'))],
        ]);

        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
            'published_at' => $request->published_at ?? now(),
            'expires_at' => $request->expires_at,
            'class_id' => $request->class_id,
        ]);

        return redirect()->route('school.announcements.index')->with('success', 'Announcement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        $user = auth()->user();
        $school = $user->school;

        if (! $school || $announcement->school_id !== $school->id) {
            abort(403, 'Unauthorized action.');
        }

        $announcement->delete();

        return back()->with('success', 'Announcement deleted successfully.');
    }
}
