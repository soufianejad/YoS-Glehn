<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index(Request $request, ?Category $category = null)
    {
        $search = $request->input('search');
        $categoryFilter = $request->input('category'); // This will now be the slug from the route
        $languageFilter = $request->input('language');
        $typeFilter = $request->input('type');

        $books = Book::with(['author', 'reviews'])->where('space', 'public')->where('status', 'published');

        if ($category) { // Apply category filter if provided via route
            $books->where('category_id', $category->id);
        } elseif ($categoryFilter) { // Apply category filter if provided via query parameter
            $categoryFromFilter = Category::where('slug', $categoryFilter)->first(); // Changed to slug
            if ($categoryFromFilter) {
                $books->where('category_id', $categoryFromFilter->id);
            }
        }

        if ($search) {
            $books->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhereHas('author', function ($query) use ($search) {
                        $query->where('title', 'like', '%'.$search.'%');
                    });
            });
        }

        if ($languageFilter) {
            $books->where('language', $languageFilter);
        }

        if ($typeFilter) {
            if ($typeFilter === 'pdf') {
                $books->whereNotNull('pdf_file');
            } elseif ($typeFilter === 'audio') {
                $books->whereNotNull('audio_file');
            }
        }

        $books = $books->paginate(10);
        $categories = Category::public()->active()->get();

        return view('library.index', compact('books', 'categories', 'search', 'category', 'languageFilter', 'typeFilter')); // Pass category to view
    }

    public function category(Request $request, Category $category)
    {
        return redirect()->route('library.index', ['category' => $category->slug, 'search' => $request->search]);
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
        ]);

        $search = $request->input('search');
        $books = Book::where('space', 'public')
            ->where('status', 'published')
            ->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            })
            ->paginate(10);
        $categories = Category::public()->active()->get();

        return view('library.search', compact('books', 'search', 'categories'));
    }

    public function popular(Request $request)
    {
        $search = $request->input('search');

        $books = Book::where('space', 'public')->where('status', 'published');

        if ($search) {
            $books->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhereHas('author', function ($query) use ($search) {
                        $query->where('title', 'like', '%'.$search.'%');
                    });
            });
        }

        $books = $books->orderByDesc('id')->paginate(10); // Placeholder for popularity, ideally based on reads/views
        $categories = Category::public()->active()->get();

        return view('library.popular', compact('books', 'categories', 'search'));
    }

    public function recent(Request $request)
    {
        $search = $request->input('search');

        $books = Book::where('space', 'public')->where('status', 'published');

        if ($search) {
            $books->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhereHas('author', function ($query) use ($search) {
                        $query->where('title', 'like', '%'.$search.'%');
                    });
            });
        }

        $books = $books->latest()->paginate(10);
        $categories = Category::public()->active()->get();

        return view('library.recent', compact('books', 'categories', 'search'));
    }
}
