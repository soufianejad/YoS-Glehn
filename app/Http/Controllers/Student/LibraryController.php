<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AudioProgress;
use App\Models\Book;
use App\Models\Category;
use App\Models\ReadingProgress;
use App\Services\BadgeService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class LibraryController extends Controller
{
    protected BadgeService $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    public function index(Request $request, ?Category $category = null) // Add optional Category
    {
        $student = auth()->user();
        $search = $request->input('search');

        $books = Book::with(['author', 'reviews', 'readingProgress' => function ($query) use ($student) {
            $query->where('user_id', $student->id);
        }])->where('space', 'educational')
            ->where('status', 'published');

        if ($category) { // Apply category filter if provided
            $books->where('category_id', $category->id);
        }

        if ($search) {
            $books->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhereHas('author', function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        $books = $books->paginate(10);
        $categories = Category::educational()->active()->get();

        return view('student.library.index', compact('books', 'categories', 'search', 'category')); // Pass category to view
    }

    public function recommended(Request $request)
    {
        $student = auth()->user();
        $search = $request->input('search');

        $books = Book::where('space', 'educational')->where('status', 'published')
            ->with(['readingProgress' => function ($query) use ($student) {
                $query->where('user_id', $student->id);
            }]);

        if ($search) {
            $books->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhereHas('author', function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        $books = $books->inRandomOrder()->paginate(10);
        $categories = Category::educational()->active()->get();

        return view('student.library.recommended', compact('books', 'categories', 'search'));
    }

    public function assigned(Request $request)
    {
        $student = auth()->user();
        $search = $request->input('search');
        $assignedBooks = collect();

        foreach ($student->classes as $class) {
            $assignedBooks = $assignedBooks->merge($class->bookAssignments()->with('book')->get()->pluck('book'));
        }

        $assignedBooks = $assignedBooks->unique('id');

        if ($search) {
            $assignedBooks = $assignedBooks->filter(function ($book) use ($search) {
                return Str::contains(strtolower($book->title), strtolower($search)) ||
                       Str::contains(strtolower($book->description), strtolower($search));
            });
        }

        // Manually paginate the filtered collection
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentPageSearchResults = $assignedBooks->slice(($page - 1) * $perPage, $perPage)->all();
        $books = new LengthAwarePaginator($currentPageSearchResults, count($assignedBooks), $perPage);
        $books->withPath(request()->url())->appends(request()->query());

        $categories = Category::educational()->active()->get();

        return view('student.library.assigned', compact('books', 'categories', 'search'));
    }

    public function category(Request $request, Category $category)
    {
        return redirect()->route('student.library.index', ['category' => $category->slug, 'search' => $request->search]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $books = Book::where('space', 'educational')
            ->where('status', 'published')
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', '%'.$query.'%')
                    ->orWhere('description', 'like', '%'.$query.'%');
            })
            ->paginate(10);
        $categories = Category::educational()->active()->get();

        return view('student.library.search', compact('books', 'query', 'categories'));
    }

    public function show(Book $book)
    {

        $student = auth()->user();

        // Ensure the book is educational or assigned to the student's school/class

        if ($book->space !== 'educational') {

            abort(403, 'Access denied to this book.');

        }

        $book->load([

            'readingProgress' => function ($query) use ($student) {

                $query->where('user_id', $student->id);

            },

            'quizzes' => function ($query) {

                $query->where('is_active', true);

            },

            'reviews' => function ($query) { // Load only approved reviews

                $query->where('is_approved', true)->latest();

            },

            'author', // Eager load author

            'category', // Eager load category

        ]);

        return view('student.book.show', compact('book'));

    }

    public function read(Book $book)
    {
        $student = auth()->user();

        // Ensure the book is educational or assigned to the student's school/class
        if ($book->space !== 'educational') {
            abort(403, 'Access denied to this book.');
        }
        if (! $book->pdf_file) {
            abort(404, 'PDF not available for this book.');
        }

        $readingProgress = ReadingProgress::where('user_id', $student->id)
            ->where('book_id', $book->id)
            ->first();

        $initialPage = $readingProgress ? $readingProgress->current_page : 0;

        return view('student.book.read', compact('book', 'initialPage'));
    }

    public function listen(Book $book)
    {
        // Ensure the book is educational or assigned to the student's school/class
        if ($book->space !== 'educational') {
            abort(403, 'Access denied to this book.');
        }
        if (! $book->audio_file) {
            abort(404, 'Audio not available for this book.');
        }

        return view('student.book.listen', compact('book'));
    }

    public function updateReadingProgress(Request $request, Book $book)
    {
        $request->validate([
            'total_pages' => 'required|integer|min:1',
            'current_page' => 'required|integer|min:0|lte:total_pages',
            'time_spent' => 'nullable|integer|min:0',
        ]);

        $user = auth()->user();

        $progress = ReadingProgress::firstOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id],
            ['current_page' => 0, 'total_pages' => $book->pdf_pages ?? $request->total_pages, 'progress_percentage' => 0, 'time_spent' => 0]
        );

        $progress->current_page = $request->current_page;
        $progress->total_pages = $book->pdf_pages ?? $request->total_pages;
        $progress->progress_percentage = ($request->current_page / $progress->total_pages) * 100;
        $progress->time_spent += $request->time_spent ?? 0;
        $progress->last_read_at = now();

        if ($progress->current_page >= $progress->total_pages) {
            $progress->completed_at = now();
        }

        $progress->save();

        $this->badgeService->checkAndAwardBadges($user);

        return response()->json(['message' => 'Reading progress updated.', 'progress' => $progress]);
    }

    public function updateAudioProgress(Request $request, Book $book)
    {
        $request->validate([
            'total_duration' => 'required|integer|min:1',
            'current_position' => 'required|integer|min:0|lte:total_duration',
            'playback_speed' => 'nullable|numeric|min:0.5|max:3',
        ]);

        $user = auth()->user();

        $progress = AudioProgress::firstOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id],
            ['current_position' => 0, 'total_duration' => $book->audio_duration ?? $request->total_duration, 'progress_percentage' => 0, 'playback_speed' => 1.0]
        );

        $progress->current_position = $request->current_position;
        $progress->total_duration = $book->audio_duration ?? $request->total_duration;
        $progress->progress_percentage = ($request->current_position / $progress->total_duration) * 100;
        $progress->playback_speed = $request->playback_speed ?? 1.0;
        $progress->last_listened_at = now();

        if ($progress->current_position >= $progress->total_duration) {
            $progress->completed_at = now();
        }

        $progress->save();
        
        $this->badgeService->checkAndAwardBadges($user);

        return response()->json(['message' => 'Audio progress updated.', 'progress' => $progress]);
    }

    public function download(Book $book)
    {
        // Ensure the book is educational or assigned to the student's school/class
        if ($book->space !== 'educational') {
            abort(403, 'Access denied to this book.');
        }
        if (! $book->pdf_file) {
            abort(404, 'PDF not available for this book.');
        }

        $filePath = storage_path('app/public/'.$book->pdf_file);

        if (! file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $book->slug.'.pdf');
    }

    public function servePdf(Book $book)
    {
        // Authorization: Ensure the book is educational or assigned to the student's school/class
        // and that the authenticated user has access.
        $user = auth()->user();

        if ($book->space !== 'educational') {
            abort(403, 'Access denied to this book.');
        }

        // You might add more complex authorization here, e.g., checking if the student is assigned the book
        // if ($user->cannot('read', $book)) { // Assuming a policy exists
        //     abort(403, 'You do not have permission to read this book.');
        // }

        if (! $book->pdf_file) {
            abort(404, 'PDF not available for this book.');
        }

        $path = storage_path('app/'.$book->private_pdf_path); // Use the accessor

        if (! file_exists($path)) {
            abort(404, 'PDF file not found on server.');
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$book->slug.'.pdf"',
        ]);
    }
}
