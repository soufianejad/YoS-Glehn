<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AudioProgress;
use App\Models\Book;
use App\Models\BookFile;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\ReadingProgress;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Subscription;
use App\Services\BadgeService;
use App\Services\NotificationService;
use App\Services\RevenueCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

        $book->load('files'); // Eager load multi-language files
        $languagesSetting = Setting::where('key', 'platform.available_languages')->first();
        $availableLanguages = $languagesSetting ? json_decode($languagesSetting->value, true) : [];

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
            'relatedBooks',
            'availableLanguages'
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

    public function read(Book $book, Request $request)
    {
        $bookFile = null;
        if ($request->has('file_id') && $request->file_id !== 'default') {
            $bookFile = BookFile::where('book_id', $book->id)
                                ->where('id', $request->file_id)
                                ->where('file_type', 'pdf')
                                ->first();
            if (!$bookFile) {
                abort(404, 'Version PDF non disponible pour ce livre dans la langue sélectionnée.');
            }
        }

        // Determine which PDF file and page count to use
        $pdfPath = $bookFile ? $bookFile->path : $book->pdf_file;
        $pdfPages = $bookFile ? $bookFile->pages : $book->pdf_pages;

        if (! $pdfPath) {
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

        $fileId = $request->query('file_id', 'default');
            
        $token = Str::random(40);
        session(['pdf_access_token_'.$book->id => $token]);

        return view('book.read', compact('book', 'initialPage', 'token', 'canDownload', 'relatedBooks', 'bookFile', 'fileId'));
    }

    public function servePdfContent(Request $request, Book $book)
    {
        // Validate the single-use token
        $token = $request->query('_token');
        $sessionToken = session('pdf_access_token_'.$book->id);

        if (! $token || ! $sessionToken || ! hash_equals($sessionToken, $token)) {
            abort(403, 'Jeton d\'accès invalide ou expiré.');
        }

        // Invalidate the token to prevent reuse (commented out to prevent issues with range requests)
        // $request->session()->forget('pdf_access_token');

        if (! $this->hasPdfAccess($book)) {
            abort(403, 'Accès non autorisé.');
        }

        $filePath = null;
        $fileToServe = null;
        $fileId = $request->query('file_id', 'default');

        if ($fileId !== 'default') {
            $bookFile = BookFile::where('book_id', $book->id)
                                ->where('id', $fileId)
                                ->where('file_type', 'pdf')
                                ->first();
            if ($bookFile) {
                $fileToServe = $bookFile->path;
            }
        } else {
            $fileToServe = $book->pdf_file;
        }

        if (! $fileToServe) {
            abort(404, 'PDF non disponible pour ce livre.');
        }

        // Check the private disk first, then the public disk as a fallback.
        if (Storage::disk('local')->exists($fileToServe)) {
            $filePath = Storage::disk('local')->path($fileToServe);
        } elseif (Storage::disk('public')->exists($fileToServe)) {
            $filePath = Storage::disk('public')->path($fileToServe);
        } else {
            abort(404, 'Fichier non trouvé sur le serveur.');
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

    public function listen(Book $book, Request $request)
    {
        $bookFile = null;
        $fileId = $request->query('file_id', 'default');

        if ($fileId !== 'default') {
            $bookFile = BookFile::where('book_id', $book->id)
                                ->where('id', $fileId)
                                ->where('file_type', 'audio')
                                ->first();
            if (!$bookFile) {
                abort(404, 'Version audio non disponible pour ce livre dans la langue sélectionnée.');
            }
        }

        // Determine which audio file and duration to use
        $audioPath = $bookFile ? $bookFile->path : $book->audio_file;
        $audioDuration = $bookFile ? $bookFile->duration : $book->audio_duration;

        if (! $audioPath) {
            abort(404, 'Audio non disponible pour ce livre.');
        }

        // Check for access
        if (! $this->hasPdfAccess($book)) {
             abort(403, 'Accès non autorisé. Vous devez acheter cet audio ou avoir un abonnement actif.');
        }

        $initialPosition = 0;
        if (auth()->check()) {
            $user = auth()->user();
            $audioProgress = AudioProgress::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->first();
            $initialPosition = $audioProgress ? $audioProgress->current_position : 0;
        }

        $token = Str::random(40);
        session(['audio_access_token_'.$book->id => $token]);

        return view('book.listen', compact('book', 'initialPosition', 'bookFile', 'audioPath', 'audioDuration', 'token', 'fileId'));
    }

    public function serveAudioContent(Request $request, Book $book)
    {
        $token = $request->query('_token');
        $sessionToken = session('audio_access_token_'.$book->id);

        if (! $token || ! $sessionToken || ! hash_equals($sessionToken, $token)) {
            abort(403, 'Jeton d\'accès invalide ou expiré.');
        }

        $fileId = $request->query('file_id', 'default');
        $fileToServe = null;

        if ($fileId !== 'default') {
            $bookFile = BookFile::where('book_id', $book->id)
                                ->where('id', $fileId)
                                ->where('file_type', 'audio')
                                ->first();
            if ($bookFile) {
                $fileToServe = $bookFile->path;
            }
        } else {
            $fileToServe = $book->audio_file;
        }

        if (! $fileToServe) {
            abort(404, 'Audio non disponible pour ce livre.');
        }

        $filePath = null;
        if (Storage::disk('public')->exists($fileToServe)) {
            $filePath = Storage::disk('public')->path($fileToServe);
        } elseif (Storage::disk('local')->exists($fileToServe)) {
            $filePath = Storage::disk('local')->path($fileToServe);
        } else {
            abort(404, 'Fichier audio non trouvé sur le serveur.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'audio/mpeg',
            'Accept-Ranges' => 'bytes'
        ]);
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

    public function purchasePdf(Book $book, Request $request)
    {
        $request->validate([
            'book_file_id' => 'nullable|integer|exists:book_files,id',
        ]);

        $user = auth()->user();

        $purchaseType = $book->is_downloadable ? 'pdf_download' : 'pdf';

        // Check if the user has already purchased this PDF (for the specific book, not necessarily specific file)
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
            'book_file_id' => $request->book_file_id, // Store the specific book_file_id if provided
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

    public function secureDownload(Book $book, Request $request)
    {
        if (! $book->is_downloadable) {
            abort(404, 'Ce livre n\'est pas disponible au téléchargement.');
        }

        $fileToDownload = null;
        $bookFile = null;

        if ($request->has('file_id') && $request->file_id !== 'default') {
            $bookFile = BookFile::where('book_id', $book->id)
                                ->where('id', $request->file_id)
                                ->where('file_type', 'pdf')
                                ->first();
            if ($bookFile) {
                $fileToDownload = $bookFile->path;
            }
        } else {
            $fileToDownload = $book->pdf_file;
        }

        if (! $fileToDownload) {
            abort(404, 'Le fichier PDF n\'est pas disponible pour ce livre.');
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

        $filePath = storage_path('app/'.$fileToDownload);

        if (! file_exists($filePath)) {
            abort(404, 'Fichier non trouvé.');
        }

        return response()->download($filePath, Str::slug($book->title).'.pdf');
    }

    public function purchaseAudio(Book $book, Request $request)
    {
        $request->validate([
            'book_file_id' => 'nullable|integer|exists:book_files,id',
        ]);

        $user = auth()->user();

        // Check if the user has already purchased this audio book (for the specific book, not necessarily specific file)
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
            'book_file_id' => $request->book_file_id, // Store the specific book_file_id if provided
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
