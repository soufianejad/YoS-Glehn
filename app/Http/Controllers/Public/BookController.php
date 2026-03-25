<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookFile;
use App\Models\Category;
use App\Models\ReadingProgress;
use App\Models\AudioProgress;
use App\Models\Purchase;
use App\Models\Review;
use App\Models\Setting;
use App\Services\BadgeService;
use App\Services\PaymentService;
use App\Services\NotificationService;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookController extends Controller
{
    protected $badgeService;
    protected $paymentService;
    protected $notificationService;

    public function __construct(BadgeService $badgeService, PaymentService $paymentService, NotificationService $notificationService)
    {
        $this->badgeService = $badgeService;
        $this->paymentService = $paymentService;
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category');
        $space = $request->input('space', 'public');

        $query = Book::with(['author', 'category'])
            ->where('status', 'published')
            ->where('space', $space);

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $books = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('book.index', compact('books', 'categories', 'search', 'categoryId', 'space'));
    }

    public function show(Book $book)
    {
        $book->load(['author', 'category', 'reviews.user', 'files']);
        
        $readingProgress = null;
        $audioProgress = null;

        if (auth()->check()) {
            $readingProgress = ReadingProgress::where('user_id', auth()->id())
                ->where('book_id', $book->id)
                ->first();
            
            $audioProgress = AudioProgress::where('user_id', auth()->id())
                ->where('book_id', $book->id)
                ->first();
        }

        // Available languages for this book (from book_files)
        $availableLanguages = $book->files->pluck('language')->unique();

        // Calculate prices
        $finalPdfPrice = $book->pdf_price;
        $finalAudioPrice = $book->audio_price;

        // Check if user has purchased the book
        $hasPurchasedBook = false;
        if (auth()->check()) {
            $hasPurchasedBook = Purchase::where('user_id', auth()->id())
                ->where('book_id', $book->id)
                ->exists();
        }

        // Check if user has active subscription
        $hasActiveSubscription = false;
        if (auth()->check()) {
             $hasActiveSubscription = auth()->user()->subscriptions()->where('status', 'active')->exists();
        }

        // Related books
        $relatedBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->where('status', 'published')
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('book.show', compact(
            'book',
            'readingProgress',
            'audioProgress',
            'finalPdfPrice',
            'hasPurchasedBook',
            'hasActiveSubscription',
            'relatedBooks',
            'availableLanguages'
        ));
    }

    public function read(Book $book, Request $request)
    {
        $bookFile = null;
        $fileId = $request->query('file_id', 'default');
        
        if ($fileId !== 'default') {
            $bookFile = BookFile::where('book_id', $book->id)
                                ->where('id', $fileId)
                                ->where('file_type', 'pdf')
                                ->first();
        }

        // Determine which PDF file and page count to use
        $pdfPath = $bookFile ? $bookFile->path : $book->pdf_file;
        $pdfPages = $bookFile ? $bookFile->pages : $book->pdf_pages;

        if (! $pdfPath) {
            abort(404, __('PDF non disponible pour ce livre.'));
        }

        $initialPage = 0;
        $canDownload = false;
        if (auth()->check()) {
            $user = auth()->user();
            $readingProgress = ReadingProgress::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->first();
            $initialPage = $readingProgress ? $readingProgress->current_page : 0;
            $canDownload = $book->is_downloadable;
        }

        $relatedBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->where('status', 'published')
            ->inRandomOrder()
            ->take(4)
            ->get();
            
        return view('book.read', compact('book', 'initialPage', 'canDownload', 'relatedBooks', 'bookFile', 'fileId', 'pdfPages', 'pdfPath'));
    }

    public function servePdfContent(Book $book, Request $request)
    {
        $fileId = $request->query('file_id', 'default');
        $pdfPath = null;

        if ($fileId !== 'default') {
            $bookFile = BookFile::where('book_id', $book->id)
                                ->where('id', $fileId)
                                ->where('file_type', 'pdf')
                                ->first();
            $pdfPath = $bookFile ? $bookFile->path : null;
        } else {
            $pdfPath = $book->pdf_file;
        }

        if (!$pdfPath) {
            abort(404);
        }

        // Try to find the file in public disk first, then local
        if (Storage::disk('public')->exists($pdfPath)) {
            $path = Storage::disk('public')->path($pdfPath);
        } elseif (Storage::exists($pdfPath)) {
            $path = Storage::path($pdfPath);
        } else {
            abort(404, __('Fichier PDF introuvable.'));
        }

        $disposition = $request->query('download') ? 'attachment' : 'inline';

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="' . $book->slug . '.pdf"'
        ]);
    }

    public function listen(Book $book, Request $request)
    {
        $bookFile = null;
        $fileId = $request->query('file_id', 'default');

        if ($fileId !== 'default') {
            $bookFile = BookFile::where('book_id', $book->id)
                                ->where('id', $fileId)
                                ->where('file_type', 'audio')
                                ->first();
        }

        // Determine which audio file to use
        $audioPath = $bookFile ? $bookFile->path : $book->audio_file;

        if (! $audioPath) {
            abort(404, __('Audio non disponible pour ce livre.'));
        }

        $initialPosition = 0;
        if (auth()->check()) {
            $user = auth()->user();
            $audioProgress = AudioProgress::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->first();
            $initialPosition = $audioProgress ? $audioProgress->current_position : 0;
        }

        return view('book.listen', compact('book', 'initialPosition', 'bookFile', 'audioPath', 'fileId'));
    }

    public function serveAudioContent(Book $book, Request $request)
    {
        $fileId = $request->query('file_id', 'default');
        $audioPath = null;

        if ($fileId !== 'default') {
            $bookFile = BookFile::where('book_id', $book->id)
                                ->where('id', $fileId)
                                ->where('file_type', 'audio')
                                ->first();
            $audioPath = $bookFile ? $bookFile->path : null;
        } else {
            $audioPath = $book->audio_file;
        }

        if (!$audioPath) {
            abort(404);
        }

        if (Storage::disk('public')->exists($audioPath)) {
            $path = Storage::disk('public')->path($audioPath);
        } elseif (Storage::exists($audioPath)) {
            $path = Storage::path($audioPath);
        } else {
            abort(404, __('Fichier audio introuvable.'));
        }

        return response()->file($path, [
            'Content-Type' => 'audio/mpeg',
            'Accept-Ranges' => 'bytes'
        ]);
    }

    public function updateReadingProgress(Request $request, Book $book)
    {
        $request->validate([
            'total_pages' => 'required|integer|min:1',
            'current_page' => 'required|integer|min:0|lte:total_pages',
            'time_spent' => 'nullable|integer|min:0',
            'book_file_id' => 'nullable|integer|exists:book_files,id',
        ]);

        $user = auth()->user();
        $bookFile = null;
        $pdfPages = $book->pdf_pages;

        if ($request->has('book_file_id')) {
            $bookFile = BookFile::find($request->book_file_id);
            if ($bookFile && $bookFile->file_type === 'pdf') {
                $pdfPages = $bookFile->pages;
            }
        }

        $progress = ReadingProgress::firstOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id, 'book_file_id' => $request->book_file_id],
            ['current_page' => 0, 'total_pages' => $pdfPages ?? $request->total_pages, 'progress_percentage' => 0, 'time_spent' => 0]
        );

        $progress->current_page = $request->current_page;
        $progress->total_pages = $pdfPages ?? $request->total_pages;
        $progress->progress_percentage = ($request->current_page / $progress->total_pages) * 100;
        $progress->time_spent += $request->time_spent ?? 0;
        $progress->last_read_at = now();

        if ($progress->current_page >= $progress->total_pages) {
            $progress->completed_at = now();
        }

        $progress->save();
        
        $this->badgeService->checkAndAwardBadges($user);

        return response()->json(['message' => __('Reading progress updated.'), 'progress' => $progress]);
    }

    public function updateAudioProgress(Request $request, Book $book)
    {
        $request->validate([
            'total_duration' => 'required|integer|min:1',
            'current_position' => 'required|integer|min:0|lte:total_duration',
            'playback_speed' => 'nullable|numeric|min:0.5|max:3',
            'book_file_id' => 'nullable|integer|exists:book_files,id',
        ]);

        $user = auth()->user();
        $bookFile = null;
        $audioDuration = $book->audio_duration;

        if ($request->has('book_file_id')) {
            $bookFile = BookFile::find($request->book_file_id);
            if ($bookFile && $bookFile->file_type === 'audio') {
                $audioDuration = $bookFile->duration;
            }
        }

        $progress = AudioProgress::firstOrCreate(
            ['user_id' => $user->id, 'book_id' => $book->id, 'book_file_id' => $request->book_file_id],
            ['current_position' => 0, 'total_duration' => $audioDuration ?? $request->total_duration, 'progress_percentage' => 0, 'playback_speed' => 1.0]
        );

        $progress->current_position = $request->current_position;
        $progress->total_duration = $audioDuration ?? $request->total_duration;
        $progress->progress_percentage = ($request->current_position / $progress->total_duration) * 100;
        $progress->playback_speed = $request->playback_speed ?? 1.0;
        $progress->last_listened_at = now();

        $progress->save();
        
        $this->badgeService->checkAndAwardBadges($user);

        return response()->json(['message' => __('Audio progress updated.'), 'progress' => $progress]);
    }

    public function storeReview(Request $request, Book $book)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
        ]);

        $review = new Review();
        $review->user_id = auth()->id();
        $review->book_id = $book->id;
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->status = 'pending';
        $review->save();

        // 1. Notify the Author
        if ($book->author_id) {
            $author = User::find($book->author_id);
            if ($author) {
                $this->notificationService->sendNotification(
                    $author,
                    __('Nouvel avis reçu'),
                    __('Un lecteur a laissé un avis sur votre livre ":title". Il est en attente de validation.', ['title' => $book->title]),
                    route('author.books.show', $book->id),
                    'info'
                );
            }
        }

        // 2. Notify the Admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $this->notificationService->sendNotification(
                $admin,
                __('Nouvel avis à modérer'),
                __('Un nouvel avis a été posté sur le livre ":title" par :user.', [
                    'title' => $book->title,
                    'user' => auth()->user()->name
                ]),
                route('admin.books.index'), // Link to moderation or book management
                'warning'
            );
        }

        return back()->with('success', __('Votre avis a été soumis avec succès et est en cours de validation par l\'équipe.'));
    }

    public function updateReview(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'pending',
        ]);

        return back()->with('success', __('Votre avis a été mis à jour.'));
    }

    public function deleteReview(Review $review)
    {
        $this->authorize('delete', $review);
        $review->delete();

        return back()->with('success', __('Votre avis a été supprimé.'));
    }

    public function checkout(Book $book, Request $request)
    {
        $type = $request->input('type', 'pdf');
        $price = $type === 'pdf' ? $book->pdf_price : $book->audio_price;
        $methods = $this->paymentService->getAvailablePaymentMethods();

        return view('book.checkout', compact('book', 'type', 'price', 'methods'));
    }

    public function purchasePdf(Book $book, Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'network' => 'required|string',
        ]);

        $payment = Payment::create([
            'user_id' => auth()->id(),
            'transaction_id' => 'BOOK-PDF-' . strtoupper(Str::random(10)),
            'payment_type' => 'book_pdf',
            'book_id' => $book->id,
            'amount' => $book->pdf_price,
            'currency' => 'XOF',
            'payment_method' => $request->network === 'CARD' ? 'card' : 'mobile_money',
            'payment_provider' => $request->network,
            'status' => 'pending',
            'payment_details' => ['purchase_type' => 'pdf'],
        ]);

        return $this->paymentService->initiatePayment($request, $payment);
    }

    public function purchaseAudio(Book $book, Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'network' => 'required|string',
        ]);

        $payment = Payment::create([
            'user_id' => auth()->id(),
            'transaction_id' => 'BOOK-AUDIO-' . strtoupper(Str::random(10)),
            'payment_type' => 'book_audio',
            'book_id' => $book->id,
            'amount' => $book->audio_price,
            'currency' => 'XOF',
            'payment_method' => $request->network === 'CARD' ? 'card' : 'mobile_money',
            'payment_provider' => $request->network,
            'status' => 'pending',
            'payment_details' => ['purchase_type' => 'audio'],
        ]);

        return $this->paymentService->initiatePayment($request, $payment);
    }

    public function secureDownload(Book $book, Request $request)
    {
        if (!auth()->user()->hasAccessToBook($book)) {
            abort(403);
        }

        $fileId = $request->query('file_id', 'default');
        $pdfPath = null;
        $fileName = $book->slug;

        if ($fileId !== 'default') {
            $bookFile = BookFile::where('book_id', $book->id)
                                ->where('id', $fileId)
                                ->where('file_type', 'pdf')
                                ->first();
            if ($bookFile) {
                $pdfPath = $bookFile->path;
                $fileName .= '_' . $bookFile->language;
            }
        } else {
            $pdfPath = $book->pdf_file;
        }

        if (!$pdfPath) {
            abort(404, __('Fichier PDF introuvable.'));
        }

        // Ensure we check the correct disk
        if (Storage::disk('public')->exists($pdfPath)) {
            return Storage::disk('public')->download($pdfPath, $fileName . '.pdf');
        } elseif (Storage::exists($pdfPath)) {
            return Storage::download($pdfPath, $fileName . '.pdf');
        }

        abort(404, __('Fichier introuvable sur le serveur.'));
    }
}
