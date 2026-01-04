<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookManagementController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $authorFilter = $request->input('author');
        $categoryFilter = $request->input('category');
        $statusFilter = $request->input('status');
        $spaceFilter = $request->input('space');

        $booksQuery = Book::with('author', 'category');

        if ($search) {
            $booksQuery->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if ($authorFilter) {
            $booksQuery->where('author_id', $authorFilter);
        }

        if ($categoryFilter) {
            $booksQuery->where('category_id', $categoryFilter);
        }

        if ($statusFilter) {
            $booksQuery->where('status', $statusFilter);
        }
        
        if ($spaceFilter) {
            $booksQuery->where('space', $spaceFilter);
        }

        $books = $booksQuery->latest()->paginate(10)->withQueryString();

        $authors = User::where('role', 'author')->orderBy('last_name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.books.index', compact(
            'books', 'search', 'authors', 'categories', 
            'authorFilter', 'categoryFilter', 'statusFilter', 'spaceFilter'
        ));
    }

    public function pending()
    {
        $books = Book::with('author', 'category')->where('status', 'pending')->paginate(10);

        return view('admin.books.pending', compact('books'));
    }

    public function create()
    {
        $authors = User::where('role', 'author')->get();
        $categories = Category::all();

        return view('admin.books.create', compact('authors', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'author_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pdf_file' => 'nullable|mimes:pdf|max:10000',
            'audio_file' => 'nullable|mimes:mp3,wav,ogg|max:20000',
            'pdf_pages' => 'nullable|integer|min:1',
            'audio_duration' => 'nullable|integer|min:1',
            'isbn' => 'nullable|string|max:255|unique:books',
            'published_year' => 'nullable|integer|min:1000|max:'.(date('Y') + 1),
            'language' => 'nullable|string|max:255',
            'space' => 'required|string|in:public,educational,adult',
            'content_type' => 'required|string|in:free,premium',
            'pdf_price' => 'nullable|numeric|min:0',
            'audio_price' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:draft,pending,published,archived',
        ]);

        $bookData = $request->except(['cover_image', 'pdf_file', 'audio_file']);

        if ($request->hasFile('cover_image')) {
            $bookData['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }
        if ($request->hasFile('pdf_file')) {
            $bookData['pdf_file'] = $request->file('pdf_file')->store('books/pdfs');
        }
        if ($request->hasFile('audio_file')) {
            $bookData['audio_file'] = $request->file('audio_file')->store('books/audios', 'public');
        }

        $bookData['slug'] = Str::slug($request->title);

        Book::create($bookData);

        return redirect()->route('admin.books.index')->with('success', 'Book created successfully.');
    }

    public function show(Book $book)
    {
        return view('admin.books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        $authors = User::where('role', 'author')->get();
        $categories = Category::all();

        return view('admin.books.edit', compact('book', 'authors', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'author_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pdf_file' => 'nullable|mimes:pdf|max:10000',
            'audio_file' => 'nullable|mimes:mp3,wav,ogg|max:20000',
            'pdf_pages' => 'nullable|integer|min:1',
            'audio_duration' => 'nullable|integer|min:1',
            'isbn' => 'nullable|string|max:255|unique:books,isbn,'.$book->id,
            'published_year' => 'nullable|integer|min:1000|max:'.(date('Y') + 1),
            'language' => 'nullable|string|max:255',
            'space' => 'required|string|in:public,educational,adult',
            'content_type' => 'required|string|in:free,premium',
            'pdf_price' => 'nullable|numeric|min:0',
            'audio_price' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:draft,pending,published,archived',
            'is_downloadable' => 'boolean',
        ]);

        $bookData = $request->except(['cover_image', 'pdf_file', 'audio_file']);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $bookData['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }
        if ($request->hasFile('pdf_file')) {
            if ($book->pdf_file) {
                Storage::delete($book->pdf_file);
            }
            $bookData['pdf_file'] = $request->file('pdf_file')->store('books/pdfs');
        }
        if ($request->hasFile('audio_file')) {
            if ($book->audio_file) {
                Storage::disk('public')->delete($book->audio_file);
            }
            $bookData['audio_file'] = $request->file('audio_file')->store('books/audios', 'public');
        }

        $bookData['slug'] = Str::slug($request->title);

        $book->update($bookData);

        return redirect()->route('admin.books.index')->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        if ($book->pdf_file) {
            Storage::delete($book->pdf_file);
        }
        if ($book->audio_file) {
            Storage::disk('public')->delete($book->audio_file);
        }

        $book->delete();

        return redirect()->route('admin.books.index')->with('success', 'Book deleted successfully.');
    }

    public function approve(Book $book)
    {
        $book->update(['status' => 'published']);

        // Notify the author
        if ($book->author) {
            $this->notificationService->sendNotification(
                $book->author,
                'Votre livre a été publié !',
                "Félicitations ! Votre livre '{$book->title}' a été approuvé et est maintenant visible sur la plateforme.",
                route('book.show', $book->slug),
                'success'
            );
        }

        return back()->with('success', 'Book approved and published.');
    }

    public function reject(Book $book)
    {
        $book->update(['status' => 'rejected']);

        return back()->with('success', 'Book rejected.');
    }

    public function feature(Book $book)
    {
        $book->update(['is_featured' => true]); // Assuming an 'is_featured' column exists

        return back()->with('success', 'Book marked as featured.');
    }

    public function changeSpace(Request $request, Book $book)
    {
        $request->validate([
            'space' => 'required|string|in:public,educational,adult',
        ]);

        $book->update(['space' => $request->space]);

        return back()->with('success', 'Book space updated successfully.');
    }
}
