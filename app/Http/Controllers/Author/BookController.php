<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookFile;
use App\Models\Category;
use App\Models\Setting;
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
        $languagesSetting = Setting::where('key', 'platform.available_languages')->first();
        $languages = $languagesSetting ? json_decode($languagesSetting->value, true) : [];

        return view('author.books.create', compact('categories', 'languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'isbn' => 'nullable|string|max:255|unique:books',
            'published_year' => 'nullable|integer|min:1000|max:'.(date('Y') + 1),
            'space' => 'required|string|in:public,educational,adult',
            'content_type' => 'required|string|in:free,premium',
            'pdf_price' => 'nullable|numeric|min:0',
            'audio_price' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:draft,pending,published,archived',
            'files.*.language' => 'required|string|max:255',
            'files.*.pdf_file' => 'nullable|mimes:pdf|max:10000',
            'files.*.audio_file' => 'nullable|mimes:mp3,wav,ogg|max:20000',
            'files.*.pdf_pages' => 'nullable|integer|min:1',
            'files.*.audio_duration' => 'nullable|integer|min:1',
        ]);

        $bookData = $request->except([
            'cover_image',
            'pdf_file',
            'audio_file',
            'pdf_pages',
            'audio_duration',
            'language',
            'files' // Exclude the files array from direct bookData
        ]);
        $bookData['author_id'] = auth()->id();

        if ($request->hasFile('cover_image')) {
            $bookData['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }

        if ($request->hasFile('pdf_file')) {
            $bookData['pdf_file'] = $request->file('pdf_file')->store('books/pdfs', 'public');
        }

        if ($request->hasFile('audio_file')) {
            $bookData['audio_file'] = $request->file('audio_file')->store('books/audios', 'public');
        }

        $bookData['slug'] = Str::slug($request->title);

        $book = Book::create($bookData);

        // Handle multi-language files
        if ($request->has('files')) {
            foreach ($request->file('files') as $fileEntry) {
                if (isset($fileEntry['pdf_file']) && $fileEntry['pdf_file']->isValid()) {
                    $path = $fileEntry['pdf_file']->store('books/pdfs', 'public');
                    $book->files()->create([
                        'language' => $fileEntry['language'],
                        'file_type' => 'pdf',
                        'path' => $path,
                        'pages' => $fileEntry['pdf_pages'] ?? null,
                    ]);
                }
                if (isset($fileEntry['audio_file']) && $fileEntry['audio_file']->isValid()) {
                    $path = $fileEntry['audio_file']->store('books/audios', 'public');
                    $book->files()->create([
                        'language' => $fileEntry['language'],
                        'file_type' => 'audio',
                        'path' => $path,
                        'duration' => $fileEntry['audio_duration'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('author.books.index')->with('success', __('Book created successfully.'));
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
        $languagesSetting = Setting::where('key', 'platform.available_languages')->first();
        $languages = $languagesSetting ? json_decode($languagesSetting->value, true) : [];
        $book->load('files'); // Eager load existing book files

        return view('author.books.edit', compact('book', 'categories', 'languages'));
    }

    public function update(Request $request, Book $book)
    {
        $this->authorize('update', $book); // Assuming a policy for Book exists

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'pdf_file' => 'nullable|mimes:pdf|max:10000', // Keep validation for default PDF
            'audio_file' => 'nullable|mimes:mp3,wav,ogg|max:20000', // Keep validation for default Audio
            'isbn' => 'nullable|string|max:255|unique:books,isbn,'.$book->id,
            'published_year' => 'nullable|integer|min:1000|max:'.(date('Y') + 1),
            'space' => 'required|string|in:public,educational,adult',
            'content_type' => 'required|string|in:free,premium',
            'pdf_price' => 'nullable|numeric|min:0',
            'audio_price' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:draft,pending,published,archived',
            'existing_files.*.language' => 'required|string|max:255',
            'existing_files.*.pdf_file' => 'nullable|mimes:pdf|max:10000',
            'existing_files.*.audio_file' => 'nullable|mimes:mp3,wav,ogg|max:20000',
            'existing_files.*.pdf_pages' => 'nullable|integer|min:1',
            'existing_files.*.audio_duration' => 'nullable|integer|min:1',
            'new_files.*.language' => 'required|string|max:255',
            'new_files.*.pdf_file' => 'nullable|mimes:pdf|max:10000',
            'new_files.*.audio_file' => 'nullable|mimes:mp3,wav,ogg|max:20000',
            'new_files.*.pdf_pages' => 'nullable|integer|min:1',
            'new_files.*.audio_duration' => 'nullable|integer|min:1',
        ]);

        $bookData = $request->except([
            'cover_image',
            'existing_files',
            'new_files',
            'deleted_files',
            // Keep pdf_file, audio_file for the main book record as per user request
            // 'pdf_file',
            // 'audio_file',
            // 'pdf_pages',
            // 'audio_duration',
            // 'language',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $bookData['cover_image'] = $request->file('cover_image')->store('books/covers', 'public');
        }
        // Handle main PDF file
        if ($request->hasFile('pdf_file')) {
            if ($book->pdf_file) {
                Storage::disk('public')->delete($book->pdf_file);
            }
            $bookData['pdf_file'] = $request->file('pdf_file')->store('books/pdfs', 'public');
        }
        // Handle main Audio file
        if ($request->hasFile('audio_file')) {
            if ($book->audio_file) {
                Storage::disk('public')->delete($book->audio_file);
            }
            $bookData['audio_file'] = $request->file('audio_file')->store('books/audios', 'public');
        }


        $bookData['slug'] = Str::slug($request->title);

        $book->update($bookData);

        // Handle deleted files
        if ($request->has('deleted_files')) {
            foreach ($request->input('deleted_files') as $fileId) {
                $bookFile = $book->files()->find($fileId);
                if ($bookFile) {
                    Storage::disk('public')->delete($bookFile->path);
                    $bookFile->delete();
                }
            }
        }

        // Handle existing multi-language files
        if ($request->has('existing_files')) {
            foreach ($request->input('existing_files') as $fileId => $fileData) {
                $bookFile = $book->files()->find($fileId);
                if ($bookFile) {
                    $bookFile->language = $fileData['language'];

                    if (isset($fileData['pdf_file']) && $fileData['pdf_file']->isValid()) {
                        Storage::disk('public')->delete($bookFile->path); // Delete old file
                        $path = $fileData['pdf_file']->store('books/pdfs', 'public');
                        $bookFile->file_type = 'pdf';
                        $bookFile->path = $path;
                        $bookFile->pages = $fileData['pdf_pages'] ?? null;
                        $bookFile->duration = null;
                    } elseif (isset($fileData['audio_file']) && $fileData['audio_file']->isValid()) {
                        Storage::disk('public')->delete($bookFile->path); // Delete old file
                        $path = $fileData['audio_file']->store('books/audios', 'public');
                        $bookFile->file_type = 'audio';
                        $bookFile->path = $path;
                        $bookFile->duration = $fileData['audio_duration'] ?? null;
                        $bookFile->pages = null;
                    }
                    $bookFile->save();
                }
            }
        }

        // Handle new multi-language files
        if ($request->has('new_files')) {
            foreach ($request->file('new_files') as $fileEntry) {
                if (isset($fileEntry['pdf_file']) && $fileEntry['pdf_file']->isValid()) {
                    $path = $fileEntry['pdf_file']->store('books/pdfs', 'public');
                    $book->files()->create([
                        'language' => $fileEntry['language'],
                        'file_type' => 'pdf',
                        'path' => $path,
                        'pages' => $fileEntry['pdf_pages'] ?? null,
                    ]);
                }
                if (isset($fileEntry['audio_file']) && $fileEntry['audio_file']->isValid()) {
                    $path = $fileEntry['audio_file']->store('books/audios', 'public');
                    $book->files()->create([
                        'language' => $fileEntry['language'],
                        'file_type' => 'audio',
                        'path' => $path,
                        'duration' => $fileEntry['audio_duration'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('author.books.index')->with('success', __('Book updated successfully.'));
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

        // Delete associated multi-language files
        foreach ($book->files as $bookFile) {
            Storage::delete($bookFile->path);
            $bookFile->delete();
        }

        $book->delete();

        return redirect()->route('author.books.index')->with('success', __('Book deleted successfully.'));
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
