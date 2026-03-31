<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Setting;
use App\Models\User;
use App\Models\Book;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\AiQuizService;

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
        $languagesSetting = Setting::where('key', 'platform.available_languages')->first();
        $languages = $languagesSetting ? json_decode($languagesSetting->value, true) : [];

        return view('admin.books.create', compact('authors', 'categories', 'languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'author_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'isbn' => 'nullable|string|max:255|unique:books',
            'published_year' => 'nullable|integer|min:1000|max:'.(date('Y') + 1),
            'space' => 'required|string|in:public,educational,adult',
            'content_type' => 'required|string|in:free,premium',
            'pdf_price' => 'nullable|numeric|min:0',
            'audio_price' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:draft,pending,published,archived',
            'is_ai_quiz_enabled' => 'boolean',
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
        $bookData['is_ai_quiz_enabled'] = $request->boolean('is_ai_quiz_enabled');

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

        return redirect()->route('admin.books.index')->with('success', __('Book created successfully.'));
    }

    public function show(Book $book)
    {
        return view('admin.books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        $authors = User::where('role', 'author')->get();
        $categories = Category::all();
        $languagesSetting = Setting::where('key', 'platform.available_languages')->first();
        $languages = $languagesSetting ? json_decode($languagesSetting->value, true) : [];
        $book->load('files'); // Eager load existing book files

        return view('admin.books.edit', compact('book', 'authors', 'categories', 'languages'));
    }

    public function update(Request $request, Book $book)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'author_id' => 'required|exists:users,id',
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
            'is_downloadable' => 'boolean',
            'is_ai_quiz_enabled' => 'boolean',
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
        $bookData['is_ai_quiz_enabled'] = $request->boolean('is_ai_quiz_enabled');

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

                    $hasNewPdf = $request->hasFile("existing_files.{$fileId}.pdf_file");
                    $hasNewAudio = $request->hasFile("existing_files.{$fileId}.audio_file");

                    if ($hasNewPdf) {
                        Storage::disk('public')->delete($bookFile->path); // Delete old file
                        $path = $request->file("existing_files.{$fileId}.pdf_file")->store('books/pdfs', 'public');
                        $bookFile->file_type = 'pdf';
                        $bookFile->path = $path;
                        $bookFile->pages = $fileData['pdf_pages'] ?? null;
                        $bookFile->duration = null;
                    } elseif ($hasNewAudio) {
                        Storage::disk('public')->delete($bookFile->path); // Delete old file
                        $path = $request->file("existing_files.{$fileId}.audio_file")->store('books/audios', 'public');
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
            foreach ($request->file('new_files') as $index => $fileEntry) {
                if (isset($fileEntry['pdf_file']) && $fileEntry['pdf_file']->isValid()) {
                    $path = $fileEntry['pdf_file']->store('books/pdfs', 'public');
                    $book->files()->create([
                        'language' => $request->input("new_files.{$index}.language"),
                        'file_type' => 'pdf',
                        'path' => $path,
                        'pages' => $request->input("new_files.{$index}.pdf_pages") ?? null,
                    ]);
                }
                if (isset($fileEntry['audio_file']) && $fileEntry['audio_file']->isValid()) {
                    $path = $fileEntry['audio_file']->store('books/audios', 'public');
                    $book->files()->create([
                        'language' => $request->input("new_files.{$index}.language"),
                        'file_type' => 'audio',
                        'path' => $path,
                        'duration' => $request->input("new_files.{$index}.audio_duration") ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.books.index')->with('success', __('Book updated successfully.'));
    }

    public function generateAiQuiz(Request $request, Book $book, AiQuizService $aiQuizService)
    {
        if (!$book->is_ai_quiz_enabled) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => __('Le générateur de quiz IA n\'est pas activé pour ce livre.')], 403);
            }
            return back()->with('error', __('Le générateur de quiz IA n\'est pas activé pour ce livre.'));
        }

        $quiz = $aiQuizService->generateQuiz($book, null);

        if ($quiz) {
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => __('Quiz global généré avec succès par l\'IA !')]);
            }
            return back()->with('success', __('Quiz global généré avec succès par l\'IA !'));
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => __('Erreur lors de la génération du quiz. Vérifiez vos clés API ou réessayez plus tard.')], 500);
        }
        return back()->with('error', __('Erreur lors de la génération du quiz. Vérifiez vos clés API ou réessayez plus tard.'));
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

        // Delete associated multi-language files
        foreach ($book->files as $bookFile) {
            Storage::delete($bookFile->path);
            $bookFile->delete();
        }

        $book->delete();

        return redirect()->route('admin.books.index')->with('success', __('Book deleted successfully.'));
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
                'book_approved'
            );
        }

        return back()->with('success', __('Book approved and published.'));
    }

    public function reject(Book $book)
    {
        $book->update(['status' => 'rejected']);

        return back()->with('success', __('Book rejected.'));
    }

    public function feature(Book $book)
    {
        $book->update(['is_featured' => true]); // Assuming an 'is_featured' column exists

        return back()->with('success', __('Book marked as featured.'));
    }

    public function changeSpace(Request $request, Book $book)
    {
        $request->validate([
            'space' => 'required|string|in:public,educational,adult',
        ]);

        $book->update(['space' => $request->space]);

        return back()->with('success', __('Book space updated successfully.'));
    }
}
