<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the parent's dashboard.
     */
    public function index()
    {
        $parent = Auth::user();
        $children = $parent->children()->with('school')->get(); // Eager load school info

        return view('parent.dashboard.index', compact('parent', 'children'));
    }

    /**
     * Display the details for a specific child.
     */
    public function showChild(User $child)
    {
        $parent = Auth::user();

        // Security check: ensure the user is the parent of the requested child.
        if ($child->parent_id !== $parent->id) {
            abort(403, 'Unauthorized action.');
        }

        // Eager load the data needed for the report
        $child->load('readingProgress.book', 'audioProgress.book', 'quizAttempts.quiz.book');

        return view('parent.dashboard.show_child', compact('parent', 'child'));
    }
}
