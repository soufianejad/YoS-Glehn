<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    /**
     * Display the specified author's public profile.
     *
     * @param  \App\Models\User  $author
     * @return \Illuminate\View\View
     */
    public function show(User $author)
    {
        // Ensure the user is actually an author
        if (!$author->isAuthor()) {
            abort(404);
        }

        $author->load(['books' => function ($query) {
            $query->withCount('reviews')->latest()->take(12);
        }]);

        return view('public.author.show', compact('author'));
    }
}
