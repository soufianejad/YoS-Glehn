<?php

namespace App\Http\Controllers\Adult;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use App\Models\ReadingProgress;
use App\Models\AudioProgress;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get IDs of adult books
        $adultBookIds = Book::where('space', 'adult')->pluck('id');

        // Stats scoped to adult books
        $totalAdultBooksPurchased = $user->purchases()
            ->whereIn('book_id', $adultBookIds)
            ->count();

        // Recently accessed adult books (reading or listening)
        $recentReading = $user->readingProgress()
            ->whereIn('book_id', $adultBookIds)
            ->with('book')
            ->latest('last_read_at')
            ->limit(3)
            ->get()->pluck('book');

        $recentListening = $user->audioProgress()
            ->whereIn('book_id', $adultBookIds)
            ->with('book')
            ->latest('updated_at')
            ->limit(3)
            ->get()->pluck('book');
        
        // Combine, get unique, and take the 3 most recent
        $recentlyAccessedBooks = $recentReading->merge($recentListening)->unique('id')->sortByDesc('updated_at')->take(3);

        // Newest adult books the user hasn't accessed
        $accessedBookIds = $user->readingProgress()->pluck('book_id')
                                ->merge($user->audioProgress()->pluck('book_id'));
        
        $newAdultBooks = Book::where('space', 'adult')
            ->where('status', 'published')
            ->whereNotIn('id', $accessedBookIds)
            ->latest('created_at')
            ->take(4)
            ->get();

        return view('adult.dashboard.index', compact(
            'totalAdultBooksPurchased',
            'recentlyAccessedBooks',
            'newAdultBooks'
        ));
    }

    public function bookmarks()
    {
        $user = auth()->user();
        $adultBookIds = Book::where('space', 'adult')->pluck('id');
        $bookmarks = $user->bookmarks()->whereIn('book_id', $adultBookIds)->with('book')->latest()->paginate(10);
        return view('adult.dashboard.bookmarks', compact('bookmarks'));
    }

    public function reviews()
    {
        $user = auth()->user();
        $adultBookIds = Book::where('space', 'adult')->pluck('id');
        $reviews = $user->reviews()->whereIn('book_id', $adultBookIds)->with('book')->latest()->paginate(10);
        return view('adult.dashboard.reviews', compact('reviews'));
    }

    public function profile()
    {
        $user = auth()->user();

        return view('adult.profile.index', compact('user'));
    }
}
