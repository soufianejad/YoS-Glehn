<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    /**
     * Display a listing of the resource for a specific book.
     */
    public function index(Book $book)
    {
        $bookmarks = Auth::user()->bookmarks()->where('book_id', $book->id)->get();

        return response()->json($bookmarks);
    }

    /**
     * Display a dedicated page with all of the user's bookmarks.
     */
    public function showAll()
    {
        $bookmarks = Auth::user()
            ->bookmarks()
            ->with('book')
            ->latest()
            ->paginate(15);

        return view('bookmarks.index', compact('bookmarks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'page_number' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
        ]);

        $bookmark = $book->bookmarks()->create([
            'user_id' => Auth::id(),
            'page_number' => $request->page_number,
            'title' => $request->title,
        ]);

        return response()->json($bookmark, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bookmark $bookmark)
    {
        // Authorize that the user owns the bookmark
        if ($bookmark->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $bookmark->update([
            'title' => $request->title,
        ]);

        return response()->json($bookmark);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bookmark $bookmark)
    {
        // Authorize that the user owns the bookmark
        if ($bookmark->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $bookmark->delete();

        return response()->json(null, 204);
    }
}
