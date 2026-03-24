<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggleFavorite(Request $request, Book $book)
    {
        $user = Auth::user();
        $isFavorited = $user->favorites()->where('book_id', $book->id)->exists();

        if ($isFavorited) {
            $user->favorites()->detach($book->id);

            return response()->json([
                'status' => 'unfavorited',
                'message' => 'Book removed from favorites.',
            ]);
        } else {
            $user->favorites()->attach($book->id);

            return response()->json([
                'status' => 'favorited',
                'message' => 'Book added to favorites.',
            ]);
        }
    }

    public function index()
    {
        $favorites = Auth::user()->favorites()->paginate(10);

        return view('dashboard.favorites', compact('favorites'));
    }
}
