<?php

namespace App\Http\Controllers\Adult;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Review;
use App\Services\RevenueCalculatorService;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    protected $revenueCalculator;

    public function __construct(RevenueCalculatorService $revenueCalculator)
    {
        $this->revenueCalculator = $revenueCalculator;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Book::where('space', 'adult')
                     ->where('status', 'published')
                     ->with([
                         'author',
                         'readingProgress' => function ($query) use ($user) {
                             $query->where('user_id', $user->id);
                         },
                     ]);

        // Filter by reading status
        if ($request->filled('reading_status')) {
            $status = $request->input('reading_status');
            if ($status === 'not_started') {
                $query->whereDoesntHave('readingProgress', function ($q) use ($user) {
                    $q->where('user_id', $user->id)->where('progress_percentage', '>', 0);
                });
            } else {
                $query->whereHas('readingProgress', function ($q) use ($user, $status) {
                    $q->where('user_id', $user->id);
                    if ($status === 'in_progress') {
                        $q->where('progress_percentage', '>', 0)->where('progress_percentage', '<', 100);
                    } elseif ($status === 'finished') {
                        $q->where('progress_percentage', '>=', 100);
                    }
                });
            }
        }
        
        // Filter by category
        if ($request->filled('category')) {
            $categorySlug = $request->input('category');
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Search by title
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('title', 'like', "%{$searchTerm}%");
        }

        $books = $query->latest()->paginate(12)->withQueryString();

        // Get categories that have at least one adult book
        $categories = Category::whereHas('books', function ($q) {
            $q->where('space', 'adult')->where('status', 'published');
        })->get();

        return view('adult.library.index', compact('books', 'categories'));
    }

    public function show(Book $book)
    {
        // Ensure the book is for adult space
        if ($book->space !== 'adult') {
            abort(403, 'Access denied to this book.');
        }

        // --- START: Grant full access to adult content ---
        // To revert, comment out the following two lines and uncomment the block below.
        $hasPurchasedPdf = true;
        $hasPurchasedAudio = true;

        /*
        // Original purchase-check logic
        $hasPurchasedPdf = false;
        $hasPurchasedAudio = false;

        if (auth()->check()) {
            $user = auth()->user();
            $hasPurchasedPdf = $user->purchases()->where('book_id', $book->id)->where('purchase_type', 'pdf')->exists();
            $hasPurchasedAudio = $user->purchases()->where('book_id', $book->id)->where('purchase_type', 'audio')->exists();
        }
        */
        // --- END: Grant full access to adult content ---

        return view('adult.book.show', compact('book', 'hasPurchasedPdf', 'hasPurchasedAudio'));
    }

    public function read(Book $book)
    {
        // Ensure the book is for adult space
        if ($book->space !== 'adult') {
            abort(403, 'Access denied to this book.');
        }
        if (! $book->pdf_file) {
            abort(404, 'PDF not available for this book.');
        }

        if (! auth()->user()->hasAccessToBook($book)) {
            abort(403, 'Access denied: You do not have permission to read this book.');
        }

        return view('adult.book.read', compact('book'));
    }

    public function listen(Book $book)
    {
        // Ensure the book is for adult space
        if ($book->space !== 'adult') {
            abort(403, 'Access denied to this book.');
        }
        if (! $book->audio_file) {
            abort(404, 'Audio not available for this book.');
        }

        if (! auth()->user()->hasAccessToBook($book)) {
            abort(403, 'Access denied: You do not have permission to listen to this book.');
        }

        return view('adult.book.listen', compact('book'));
    }

    public function purchasePdf(Book $book)
    {
        $user = auth()->user();

        // In a real application, this would integrate with a payment gateway.
        // For now, we'll simulate a successful purchase.

        // Create a dummy payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'transaction_id' => 'TRX-'.uniqid(),
            'payment_type' => 'book_pdf',
            'amount' => $book->pdf_price,
            'currency' => 'USD',
            'payment_method' => 'simulated',
            'payment_provider' => 'simulated',
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        // Create a purchase record
        Purchase::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'payment_id' => $payment->id,
            'purchase_type' => 'pdf',
            'price' => $book->pdf_price,
            'is_active' => true,
        ]);

        $this->revenueCalculator->recordRevenue($payment);

        return back()->with('success', 'PDF purchased successfully!');
    }

    public function purchaseAudio(Book $book)
    {
        $user = auth()->user();

        // In a real application, this would integrate with a payment gateway.
        // For now, we'll simulate a successful purchase.

        // Create a dummy payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'transaction_id' => 'TRX-'.uniqid(),
            'payment_type' => 'book_audio',
            'amount' => $book->audio_price,
            'currency' => 'USD',
            'payment_method' => 'simulated',
            'payment_provider' => 'simulated',
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        // Create a purchase record
        Purchase::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'payment_id' => $payment->id,
            'purchase_type' => 'audio',
            'price' => $book->audio_price,
            'is_active' => true,
        ]);

        $this->revenueCalculator->recordRevenue($payment);

        return back()->with('success', 'Audio purchased successfully!');
    }

    public function storeReview(Request $request, Book $book)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $book->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => true, // Automatically approve for now
        ]);

        return back()->with('success', 'Your review has been submitted successfully!');
    }
}
