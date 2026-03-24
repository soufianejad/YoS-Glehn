<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stats = [
            'books' => \App\Models\Book::count(),
            'authors' => \App\Models\User::where('role', 'author')->count(),
            'users' => \App\Models\User::count(),
        ];

        $popularCategories = \App\Models\Category::withCount('books')->orderBy('books_count', 'desc')->take(5)->get();
        $featuredBooks = \App\Models\Book::with('author')->withCount('reviews')->where('is_featured', true)->latest()->take(6)->get();
        $featuredAuthors = \App\Models\User::where('role', 'author')->inRandomOrder()->take(4)->get();

        return view('welcome', compact('stats', 'popularCategories', 'featuredBooks', 'featuredAuthors'));
    }

    public function dashboard()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isStudent()) {
            return redirect()->route('student.dashboard');
        } elseif ($user->isAuthor()) {
            return redirect()->route('author.dashboard');
        } elseif ($user->isSchool()) {
            return redirect()->route('school.dashboard');
        } elseif ($user->isAdultReader()) {
            return redirect()->route('adult.dashboard');
        } elseif ($user->isReader()) {
            return redirect()->route('reader.dashboard');
        } else {
            // Default for general readers or other roles
            return redirect()->route('home'); // Or a general user profile/dashboard
        }
    }

    public function profile()
    {
        $user = auth()->user();

        return view('profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:255',
        ]);

        $user->update($request->only('first_name', 'last_name', 'email', 'phone'));

        return back()->with('success', 'Profile updated successfully.');
    }

    public function redirectToProfile()
    {
        $user = auth()->user();

        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard'); // Admins might not have a dedicated profile page
            case 'author':
                return redirect()->route('author.profile');
            case 'school':
                return redirect()->route('school.dashboard'); // Or a settings page
            case 'student':
                return redirect()->route('student.dashboard'); // Or a profile page if it exists
            case 'reader':
            case 'adult_reader':
                return redirect()->route('reader.profile');
            default:
                return redirect()->route('home');
        }
    }
}
