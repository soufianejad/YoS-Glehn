<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $author = auth()->user();
        $search = $request->input('search');

        $booksQuery = $author->books()
            ->with(['category'])
            ->withCount(['reviews', 'purchases as sales_count'])
            ->withAvg('reviews', 'rating');

        if ($search) {
            $booksQuery->where('title', 'like', "%{$search}%");
        }
        
        $books = $booksQuery->latest()->paginate(10)->withQueryString();

        return view('author.books.index', compact('books', 'search'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('author.books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
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
        $bookData['author_id'] = auth()->id();

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

        return redirect()->route('author.books.index')->with('success', 'Book created successfully.');
    }

    public function show(Book $book)
    {
        $this->authorize('view', $book);

        // Eager load some relationships for display
        $book->load('category');

        // Get key stats
        $stats = [
            'sales' => $book->purchases()->count(),
            'revenue' => $book->revenues()->sum('author_amount'),
            'reviews_count' => $book->reviews()->count(),
            'avg_rating' => $book->reviews()->avg('rating'),
        ];

            // Get latest reviews for this book
            $recentReviews = $book->reviews()->with('user:id,first_name,last_name,avatar')->latest()->take(5)->get();
        return view('author.books.show', compact('book', 'stats', 'recentReviews'));
    }

    public function edit(Book $book)
    {
        $this->authorize('update', $book); // Assuming a policy for Book exists
        $categories = Category::all();

        return view('author.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book)
    {
        $this->authorize('update', $book); // Assuming a policy for Book exists

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
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

        return redirect()->route('author.books.index')->with('success', 'Book updated successfully.');
    }

    public function destroy(Book $book)
    {
        $this->authorize('delete', $book); // Assuming a policy for Book exists

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

        return redirect()->route('author.books.index')->with('success', 'Book deleted successfully.');
    }

    public function statistics(Book $book)
    {
        $this->authorize('view', $book);

        // 1. Key Stats for the book
        $stats = [
            'sales' => $book->purchases()->count(),
            'revenue' => $book->revenues()->sum('author_amount'),
            'reviews_count' => $book->reviews()->count(),
            'avg_rating' => $book->reviews()->avg('rating'),
            'total_reading_seconds' => $book->readingProgress()->sum('time_spent'),
            'total_listening_seconds' => $book->audioProgress()->sum('current_position'),
        ];

        // 2. Data for Charts (Sales over last 6 months)
        $salesByMonth = $book->purchases()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as total")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()->pluck('total', 'month');

        $chartLabels = collect();
        $salesData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $chartLabels->push($month->format('M Y'));
            $salesData->push($salesByMonth->get($monthKey, 0));
        }
        $chartData = ['labels' => $chartLabels, 'sales' => $salesData];

        // 3. Reader Engagement Details (Paginated)
        $readers = $book->readingProgress()
            ->with('user:id,first_name,last_name,avatar')
            ->where('progress_percentage', '>', 0)
            ->orderBy('last_read_at', 'desc')
            ->paginate(10, ['*'], 'readers_page');

        $listeners = $book->audioProgress()
            ->with('user:id,first_name,last_name,avatar')
            // ->where('progress_percentage', '>', 0) // audioProgress may not have this column
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'listeners_page');


        return view('author.books.statistics', compact('book', 'stats', 'chartData', 'readers', 'listeners'));
    }
}
