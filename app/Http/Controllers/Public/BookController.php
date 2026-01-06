<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AudioProgress;
use App\Models\Book;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\ReadingProgress;
use App\Models\Review;
use App\Models\Subscription;
use App\Services\BadgeService;
use App\Services\NotificationService;
use App\Services\RevenueCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookController extends Controller
{
    protected $revenueCalculator;

    protected $notificationService;

    protected $badgeService;

    public function __construct(
        RevenueCalculatorService $revenueCalculator,
        NotificationService $notificationService,
        BadgeService $badgeService
    ) {
        $this->revenueCalculator = $revenueCalculator;
        $this->notificationService = $notificationService;
        $this->badgeService = $badgeService;
    }

    public function show(Book $book)
    {
        $readingProgress = null;
        $audioProgress = null;
        $finalPdfPrice = $book->pdf_price;
        $discountedPdfPrice = null;
        $hasPurchasedBook = false;
        $hasActiveSubscription = false;

        if (auth()->check()) {
            $user = auth()->user();
            $readingProgress = $user->getReadingProgressFor($book);
            $audioProgress = AudioProgress::where('user_id', $user->id)->where('book_id', $book->id)->first();

            // Vérifier si l'utilisateur a un abonnement actif
            $hasActiveSubscription = $user->hasActiveSubscription();

            // Vérifier si l'utilisateur a déjà acheté le livre (lecture en ligne ou téléchargement)
            $hasPurchasedBook = Purchase::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->whereIn('purchase_type', ['pdf', 'pdf_download'])
                ->where('is_active', true)
                ->exists();

            // Appliquer la réduction pour les abonnés qui n'ont pas encore acheté le livre et si le livre est téléchargeable
            if ($hasActiveSubscription && $book->is_downloadable && ! $hasPurchasedBook) {
                $discountPercentage = config('plateform.downloads.subscription_discount_percentage', 0);
                if ($discountPercentage > 0) {
                    $discountedPdfPrice = $book->pdf_price * (1 - ($discountPercentage / 100));
                    $finalPdfPrice = $discountedPdfPrice;
                }
            }
        }

        // Fetch related books from the same category
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
            'discountedPdfPrice',
            'hasPurchasedBook',
            'hasActiveSubscription',
            'relatedBooks'
        ));
    }

    public function incrementViews(Book $book)
    {
        $book->increment('views'); // Assuming a 'views' column exists in the books table

        return response()->json(['views' => $book->views]);
    }

    private function hasPdfAccess(Book $book): bool
    {
        if (! auth()->check()) {
            return false;
        }

        $user = auth()->user();

        // Utilise la méthode centralisée dans le modèle User
        return $user->hasAccessToBook($book);
    }

    public function read(Book $book)
    {
        if (! $book->pdf_file) {
            abort(404, 'PDF non disponible pour ce livre.');
        }

        if (! $this->hasPdfAccess($book)) {
            abort(403, 'Accès non autorisé. Vous devez acheter ce livre ou avoir un abonnement actif.');
        }

        $this->badgeService->checkAndAwardBadges(auth()->user());

        $initialPage = 0;
        $canDownload = false;
        if (auth()->check()) {
            $user = auth()->user();
            $readingProgress = ReadingProgress::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->first();
            $initialPage = $readingProgress ? $readingProgress->current_page : 0;
            $canDownload = $user->canDownloadBook($book);
        }

        $relatedBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->where('status', 'published')
            ->inRandomOrder()
            ->take(4)
            ->get();
            
        $token = Str::random(40);
        session(['pdf_access_token' => $token]);

        return view('book.read', compact('book', 'initialPage', 'token', 'canDownload', 'relatedBooks'));
    }

    public function servePdfContent(Request $request, Book $book)
    {
        // Validate the single-use token
        $token = $request->query('_token');
        $sessionToken = session('pdf_access_token');

        if (! $token || ! $sessionToken || ! hash_equals($sessionToken, $token)) {
            abort(403, 'Jeton d\'accès invalide ou expiré.');
        }

        // The token is valid, so invalidate it immediately to prevent reuse
        // $request->session()->forget('pdf_access_token');

        if (! $book->pdf_file) {
            abort(404, 'PDF non disponible pour ce livre.');
        }

        if (! $this->hasPdfAccess($book)) {
            abort(403, 'Accès non autorisé.');
        }

        $filePath = storage_path('app/'.$book->pdf_file);

        if (! file_exists($filePath)) {
            abort(404, 'Fichier non trouvé.');
        }

        // Award badges for reading the book
        $this->badgeService->checkAndAwardBadges(auth()->user());

        return response()->file($filePath);
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

    /*
    public function download(Book $book)
    {
        // Implement logic to check if user has purchased or subscribed
        // For now, assuming direct download if pdf_file exists
        if (!$book->pdf_file) {
            abort(404, 'PDF not available for this book.');
        }

        $filePath = storage_path('app/public/' . $book->pdf_file);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        return response()->download($filePath, $book->slug . '.pdf');
    }
    */

    public function listen(Book $book)
    {
        if (! $book->audio_file) {
            abort(404, 'Audio not available for this book.');
        }

        $initialPosition = 0;
        if (auth()->check()) {
            $user = auth()->user();
            $audioProgress = AudioProgress::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->first();
            $initialPosition = $audioProgress ? $audioProgress->current_position : 0;
        }

        return view('book.listen', compact('book', 'initialPosition'));
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

    public function addToFavorites(Book $book)
    {
        $user = auth()->user();
        $user->favorites()->firstOrCreate(['book_id' => $book->id]);

        return back()->with('success', 'Book added to favorites!');
    }

    public function removeFromFavorites(Book $book)
    {
        $user = auth()->user();
        $user->favorites()->where('book_id', $book->id)->delete();

        return back()->with('success', 'Book removed from favorites.');
    }

    public function storeReview(Request $request, Book $book)
    {
        $request->validate([
            'rating' => [
                'required',
                'integer',
                'min:1',
                'max:5',
                function ($attribute, $value, $fail) use ($book) {
                    $exists = $book->reviews()->where('user_id', auth()->id())->exists();
                    if ($exists) {
                        $fail('You have already reviewed this book.');
                    }
                },
            ],
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = $book->reviews()->create([
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => false, // New reviews require admin approval
        ]);

        return back()->with('success', 'Review submitted successfully and is pending approval.');
    }

    public function updateReview(Request $request, Review $review)
    {
        $this->authorize('update', $review); // Assuming a policy for Review exists

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Review updated successfully!');
    }

    public function deleteReview(Review $review)
    {
        $this->authorize('delete', $review); // Assuming a policy for Review exists

        $review->delete();

        return back()->with('success', 'Review deleted successfully!');
    }

    public function purchasePdf(Book $book)
    {
        $user = auth()->user();

        $purchaseType = $book->is_downloadable ? 'pdf_download' : 'pdf';

        // Check if the user has already purchased this PDF
        $existingPurchase = Purchase::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereIn('purchase_type', ['pdf', 'pdf_download'])
            ->first();

        if ($existingPurchase) {
            return back()->with('error', __('You have already purchased this item.'));
        }

        $amount = $book->pdf_price;

        // Apply discount if user has active subscription and book is downloadable
        if ($user->hasActiveSubscription() && $book->is_downloadable) {
            $discountPercentage = config('plateform.downloads.subscription_discount_percentage', 0);
            if ($discountPercentage > 0) {
                $amount = $book->pdf_price * (1 - ($discountPercentage / 100));
            }
        }

        // Create a dummy payment record with completed status
        $payment = Payment::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'transaction_id' => 'TRX-'.uniqid(),
            'payment_type' => $purchaseType,
            'amount' => $amount,
            'currency' => 'XOF',
            'payment_method' => 'simulated',
            'payment_provider' => 'simulated',
            'status' => 'completed', // Simulate a successful payment
        ]);

        // Create an active purchase record
        Purchase::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'payment_id' => $payment->id,
            'purchase_type' => $purchaseType,
            'price' => $amount,
            'is_active' => true, // The purchase is active immediately
        ]);

        // Send notification
        $this->notificationService->sendNotification(
            $user,
            'Achat confirmé',
            "Merci pour votre achat du livre '{$book->title}'. Vous pouvez y accéder dès maintenant.",
            route('reader.library'),
            'success'
        );

        // Check for first-ever sale and notify author
        $totalSales = Purchase::where('book_id', $book->id)->count();
        if ($totalSales === 1 && $book->author) {
            $this->notificationService->sendNotification(
                $book->author,
                'Première vente !',
                "Félicitations ! Votre livre '{$book->title}' vient d'enregistrer sa toute première vente !",
                route('author.dashboard'), // Or a more specific stats page
                'success'
            );
        }

        return back()->with('success', 'Your purchase is complete! You can now download the book.');
    }

    public function secureDownload(Book $book)
    {
        if (! $book->is_downloadable || ! $book->pdf_file) {
            abort(404, 'Ce livre n\'est pas disponible au téléchargement ou le fichier PDF est manquant.');
        }

        $user = auth()->user();

        // Check if the user has purchased the downloadable PDF
        $hasPurchasedDownload = Purchase::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('purchase_type', 'pdf_download')
            ->where('is_active', true)
            ->exists();

        if (! $hasPurchasedDownload) {
            abort(403, 'Accès non autorisé. Vous devez acheter ce livre pour le télécharger.');
        }

        $filePath = storage_path('app/'.$book->pdf_file);

        if (! file_exists($filePath)) {
            abort(404, 'Fichier non trouvé.');
        }

        return response()->download($filePath, Str::slug($book->title).'.pdf');
    }

    public function purchaseAudio(Book $book)
    {
        $user = auth()->user();

        // Check if the user has already purchased this audio book
        $existingPurchase = Purchase::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('purchase_type', 'audio')
            ->first();

        if ($existingPurchase) {
            return back()->with('error', __('You have already purchased this item.'));
        }

        // Create a dummy payment record with completed status
        $payment = Payment::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'transaction_id' => 'TRX-'.uniqid(),
            'payment_type' => 'book_audio',
            'amount' => $book->audio_price,
            'currency' => 'XOF',
            'payment_method' => 'simulated',
            'payment_provider' => 'simulated',
            'status' => 'completed', // Simulate a successful payment
        ]);

        // Create an active purchase record
        Purchase::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'payment_id' => $payment->id,
            'purchase_type' => 'audio',
            'price' => $book->audio_price,
            'is_active' => true, // The purchase is active immediately
        ]);

        // Send notification
        $this->notificationService->sendNotification(
            $user,
            'Achat confirmé',
            "Merci pour votre achat de la version audio de '{$book->title}'. Vous pouvez y accéder dès maintenant.",
            route('reader.library'),
            'success'
        );

        return back()->with('success', __('Your purchase is complete! You can now listen to the book.'));
    }
}
