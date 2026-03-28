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
use App\Models\Payment;
use App\Models\User;
use App\Services\BadgeService;
use App\Services\PaymentService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookController extends Controller
{
    protected $badgeService;
    protected $paymentService;
    protected $notificationService;

    public function __construct(
        BadgeService        $badgeService,
        PaymentService      $paymentService,
        NotificationService $notificationService
    ) {
        $this->badgeService        = $badgeService;
        $this->paymentService      = $paymentService;
        $this->notificationService = $notificationService;
    }

    // =========================================================================
    // CATALOGUE
    // =========================================================================

    public function index(Request $request)
    {
        $search     = $request->input('search');
        $categoryId = $request->input('category');
        $space      = $request->input('space', 'public');

        $query = Book::with(['author', 'category'])
            ->where('status', 'published')
            ->where('space', $space);

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $books      = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('book.index', compact('books', 'categories', 'search', 'categoryId', 'space'));
    }

    public function show(Book $book)
    {
        $book->load(['author', 'category', 'reviews.user', 'files']);

        $readingProgress = null;
        $audioProgress   = null;

        if (auth()->check()) {
            $readingProgress = ReadingProgress::where('user_id', auth()->id())->where('book_id', $book->id)->first();
            $audioProgress   = AudioProgress::where('user_id', auth()->id())->where('book_id', $book->id)->first();
        }

        $availableLanguages = $book->files->pluck('language')->unique();
        $finalPdfPrice      = $book->pdf_price;

        $hasPurchasedBook = auth()->check()
            ? Purchase::where('user_id', auth()->id())->where('book_id', $book->id)->exists()
            : false;

        $hasActiveSubscription = auth()->check()
            ? auth()->user()->subscriptions()->where('status', 'active')->exists()
            : false;

        $relatedBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->where('status', 'published')
            ->inRandomOrder()->take(4)->get();

        return view('book.show', compact(
            'book', 'readingProgress', 'audioProgress',
            'finalPdfPrice', 'hasPurchasedBook', 'hasActiveSubscription',
            'relatedBooks', 'availableLanguages'
        ));
    }

    // =========================================================================
    // LECTURE / ÉCOUTE
    // =========================================================================

    public function read(Book $book, Request $request)
    {
        $fileId   = $request->query('file_id', 'default');
        $bookFile = null;

        if ($fileId !== 'default') {
            $bookFile = BookFile::where('book_id', $book->id)->where('id', $fileId)->where('file_type', 'pdf')->first();
        }

        $pdfPath  = $bookFile ? $bookFile->path : $book->pdf_file;
        $pdfPages = $bookFile ? $bookFile->pages : $book->pdf_pages;

        if (!$pdfPath) {
            abort(404, __('PDF non disponible pour ce livre.'));
        }

        $initialPage = 0;
        $canDownload = false;

        if (auth()->check()) {
            $readingProgress = ReadingProgress::where('user_id', auth()->id())->where('book_id', $book->id)->first();
            $initialPage     = $readingProgress ? $readingProgress->current_page : 0;
            $canDownload     = $book->is_downloadable;
        }

        $relatedBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)->where('status', 'published')
            ->inRandomOrder()->take(4)->get();

        return view('book.read', compact('book', 'initialPage', 'canDownload', 'relatedBooks', 'bookFile', 'fileId', 'pdfPages', 'pdfPath'));
    }

    public function servePdfContent(Book $book, Request $request)
    {
        $fileId  = $request->query('file_id', 'default');
        $pdfPath = null;

        if ($fileId !== 'default') {
            $bookFile = BookFile::where('book_id', $book->id)->where('id', $fileId)->where('file_type', 'pdf')->first();
            $pdfPath  = $bookFile ? $bookFile->path : null;
        } else {
            $pdfPath = $book->pdf_file;
        }

        if (!$pdfPath) { abort(404); }

        $path        = null;
        $disposition = $request->query('download') ? 'attachment' : 'inline';

        if (Storage::disk('public')->exists($pdfPath)) {
            $path = Storage::disk('public')->path($pdfPath);
        } elseif (Storage::exists($pdfPath)) {
            $path = Storage::path($pdfPath);
        } else {
            abort(404, __('Fichier PDF introuvable.'));
        }

        return response()->file($path, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="' . $book->slug . '.pdf"',
        ]);
    }

    public function listen(Book $book, Request $request)
    {
        $fileId   = $request->query('file_id', 'default');
        $bookFile = null;

        if ($fileId !== 'default') {
            $bookFile = BookFile::where('book_id', $book->id)->where('id', $fileId)->where('file_type', 'audio')->first();
        }

        $audioPath = $bookFile ? $bookFile->path : $book->audio_file;

        if (!$audioPath) {
            abort(404, __('Audio non disponible pour ce livre.'));
        }

        $initialPosition = 0;
        if (auth()->check()) {
            $audioProgress   = AudioProgress::where('user_id', auth()->id())->where('book_id', $book->id)->first();
            $initialPosition = $audioProgress ? $audioProgress->current_position : 0;
        }

        return view('book.listen', compact('book', 'initialPosition', 'bookFile', 'audioPath', 'fileId'));
    }

    public function serveAudioContent(Book $book, Request $request)
    {
        $fileId    = $request->query('file_id', 'default');
        $audioPath = null;

        if ($fileId !== 'default') {
            $bookFile  = BookFile::where('book_id', $book->id)->where('id', $fileId)->where('file_type', 'audio')->first();
            $audioPath = $bookFile ? $bookFile->path : null;
        } else {
            $audioPath = $book->audio_file;
        }

        if (!$audioPath) { abort(404); }

        if (Storage::disk('public')->exists($audioPath)) {
            $path = Storage::disk('public')->path($audioPath);
        } elseif (Storage::exists($audioPath)) {
            $path = Storage::path($audioPath);
        } else {
            abort(404, __('Fichier audio introuvable.'));
        }

        return response()->file($path, [
            'Content-Type'  => 'audio/mpeg',
            'Accept-Ranges' => 'bytes',
        ]);
    }

    // =========================================================================
    // PROGRESSION
    // =========================================================================

    public function updateReadingProgress(Request $request, Book $book)
    {
        $request->validate([
            'total_pages'  => 'required|integer|min:1',
            'current_page' => 'required|integer|min:0|lte:total_pages',
            'time_spent'   => 'nullable|integer|min:0',
            'book_file_id' => 'nullable|integer|exists:book_files,id',
        ]);

        $user     = auth()->user();
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

        $progress->current_page        = $request->current_page;
        $progress->total_pages         = $pdfPages ?? $request->total_pages;
        $progress->progress_percentage = ($request->current_page / $progress->total_pages) * 100;
        $progress->time_spent         += $request->time_spent ?? 0;
        $progress->last_read_at        = now();

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
            'total_duration'   => 'required|integer|min:1',
            'current_position' => 'required|integer|min:0|lte:total_duration',
            'playback_speed'   => 'nullable|numeric|min:0.5|max:3',
            'book_file_id'     => 'nullable|integer|exists:book_files,id',
        ]);

        $user          = auth()->user();
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

        $progress->current_position    = $request->current_position;
        $progress->total_duration      = $audioDuration ?? $request->total_duration;
        $progress->progress_percentage = ($request->current_position / $progress->total_duration) * 100;
        $progress->playback_speed      = $request->playback_speed ?? 1.0;
        $progress->last_listened_at    = now();
        $progress->save();

        $this->badgeService->checkAndAwardBadges($user);

        return response()->json(['message' => __('Audio progress updated.'), 'progress' => $progress]);
    }

    // =========================================================================
    // AVIS
    // =========================================================================

    public function storeReview(Request $request, Book $book)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
        ]);

        $review = Review::create([
            'user_id'     => auth()->id(),
            'book_id'     => $book->id,
            'rating'      => $request->rating,
            'comment'     => $request->comment,
            'is_approved' => false,
        ]);

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

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $this->notificationService->sendNotification(
                $admin,
                __('Nouvel avis à modérer'),
                __('Un nouvel avis a été posté sur le livre ":title" par :user.', ['title' => $book->title, 'user' => auth()->user()->name]),
                route('admin.reviews.pending'),
                'warning'
            );
        }

        return back()->with('success', __('Votre avis a été soumis avec succès et est en cours de validation par l\'équipe.'));
    }

    public function updateReview(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10',
        ]);

        $review->update(['rating' => $request->rating, 'comment' => $request->comment, 'is_approved' => false]);

        return back()->with('success', __('Votre avis a été mis à jour et est en cours de validation.'));
    }

    public function deleteReview(Review $review)
    {
        $this->authorize('delete', $review);
        $review->delete();

        return back()->with('success', __('Votre avis a été supprimé.'));
    }

    // =========================================================================
    // PAIEMENT – CHECKOUT & INITIATION
    // =========================================================================

    public function checkout(Book $book, Request $request)
    {
        $type    = $request->input('type', 'pdf');
        $price   = $type === 'pdf' ? $book->pdf_price : $book->audio_price;
        $methods = $this->paymentService->getAvailablePaymentMethods();

        return view('book.checkout', compact('book', 'type', 'price', 'methods'));
    }

    public function purchasePdf(Book $book, Request $request)
    {
        $request->validate([
            'phone'   => 'required|string',
            'network' => 'required|string',
        ]);

        $payment = Payment::create([
            'user_id'          => auth()->id(),
            'transaction_id'   => 'BOOK-PDF-' . strtoupper(Str::random(10)),
            'payment_type'     => 'book_pdf',
            'book_id'          => $book->id,
            'amount'           => $book->pdf_price,
            'currency'         => 'XOF',
            'payment_method'   => $request->network === 'CARD' ? 'card' : 'mobile_money',
            'payment_provider' => $request->network,
            'status'           => 'pending',
            'payment_details'  => ['purchase_type' => 'pdf_download'],
        ]);

        return $this->paymentService->initiatePayment($request, $payment);
    }

    public function purchaseAudio(Book $book, Request $request)
    {
        $request->validate([
            'phone'   => 'required|string',
            'network' => 'required|string',
        ]);

        $payment = Payment::create([
            'user_id'          => auth()->id(),
            'transaction_id'   => 'BOOK-AUDIO-' . strtoupper(Str::random(10)),
            'payment_type'     => 'book_audio',
            'book_id'          => $book->id,
            'amount'           => $book->audio_price,
            'currency'         => 'XOF',
            'payment_method'   => $request->network === 'CARD' ? 'card' : 'mobile_money',
            'payment_provider' => $request->network,
            'status'           => 'pending',
            'payment_details'  => ['purchase_type' => 'audio'],
        ]);

        return $this->paymentService->initiatePayment($request, $payment);
    }

    // =========================================================================
    // CALLBACKS PAIEMENT
    // =========================================================================

    /**
     * Point d'entrée unique pour tous les callbacks de prestataires de paiement.
     * Route : payment.callback → /payment/callback/{service}
     */
    public function paymentCallback(Request $request, string $service)
    {
        Log::channel('payment')->info("Callback reçu – service : {$service}", [
            'ip'   => $request->ip(),
            'body' => $request->getContent(),
        ]);

        // ------------------------------------------------------------------
        // WAVE
        // ------------------------------------------------------------------
        if ($service === 'wave') {
            $data      = $request->json()->all();
            $sessionId = $data['data']['id'] ?? null;

            if (!$sessionId) {
                return response('OK', 200);
            }

            $payment = Payment::where(function ($q) use ($sessionId) {
                $q->where('transaction_id', $sessionId)
                  ->orWhereJsonContains('payment_details->wave_id', $sessionId);
            })->where('status', 'pending')->first();

            if ($payment && ($data['data']['payment_status'] ?? '') === 'succeeded') {
                $this->validateAndFinalize($payment);
            }

            return response('OK', 200);
        }

        // ------------------------------------------------------------------
        // TOUCHPAY
        // ------------------------------------------------------------------
        if ($service === 'touchpay') {
            $body      = $request->json()->all();
            $reference = $body['partner_transaction_id'] ?? null;

            if (!$reference) {
                return response('OK', 200);
            }

            $payment = Payment::where('transaction_id', $reference)->where('status', 'pending')->first();

            if ($payment && strtoupper($body['status'] ?? '') === 'SUCCESSFUL') {
                $providerRef = $body['gu_transaction_id'] ?? null;
                $this->validateAndFinalize($payment, $providerRef);
            }

            return response('OK', 200);
        }

        // ------------------------------------------------------------------
        // PAWAPAY
        // ------------------------------------------------------------------
        if ($service === 'pawapay') {
            $body      = $request->json()->all();
            $depositId = $body['depositId'] ?? null;

            if (!$depositId) {
                return response('OK', 200);
            }

            $payment = Payment::where('transaction_id', $depositId)->where('status', 'pending')->first();

            if ($payment && strtoupper($body['status'] ?? '') === 'COMPLETED') {
                $this->validateAndFinalize($payment);
            }

            return response('OK', 200);
        }

        // ------------------------------------------------------------------
        // PAIEMENTPRO
        // ------------------------------------------------------------------
        if ($service === 'paiementpro') {
            $reference    = $request->input('referenceNumber');
            $responseCode = $request->input('responsecode');
            $payId        = $request->input('payId');

            if (!$reference) {
                return redirect()->route('home');
            }

            $payment = Payment::where('transaction_id', $reference)->where('status', 'pending')->first();

            if ($payment && $responseCode == '0') {
                $this->validateAndFinalize($payment, $payId);

                return redirect()->route('payment.success')
                    ->with('success', 'Paiement validé avec succès !');
            }

            return redirect()->route('payment.failed')
                ->with('danger', 'Transaction refusée ou introuvable.');
        }

        return response('Service non géré', 404);
    }

    // =========================================================================
    // HELPER : Validation + finalisation de paiement
    // =========================================================================

    private function validateAndFinalize(Payment $payment, ?string $providerRef = null): void
    {
        // Protection contre la double validation
        $fresh = Payment::where('id', $payment->id)->lockForUpdate()->first();

        if (!$fresh || $fresh->status !== 'pending') {
            Log::warning("Paiement déjà traité ou introuvable", ['id' => $payment->id]);
            return;
        }

        $updateData = ['status' => 'completed'];
        if ($providerRef) {
            $updateData['payment_details'] = array_merge($fresh->payment_details ?? [], ['provider_ref' => $providerRef]);
        }

        $fresh->update($updateData);

        // Appel de la finalisation métier (création Purchase / activation Subscription)
        $this->paymentService->finalizePurchase($fresh);

        Log::info("Paiement validé et finalisé", ['transaction_id' => $fresh->transaction_id, 'type' => $fresh->payment_type]);
    }

    // =========================================================================
    // TÉLÉCHARGEMENT SÉCURISÉ
    // =========================================================================

    public function secureDownload(Book $book, Request $request)
    {
        if (!auth()->user()->hasAccessToBook($book)) {
            abort(403);
        }

        $fileId   = $request->query('file_id', 'default');
        $pdfPath  = null;
        $fileName = $book->slug;

        if ($fileId !== 'default') {
            $bookFile = BookFile::where('book_id', $book->id)->where('id', $fileId)->where('file_type', 'pdf')->first();
            if ($bookFile) {
                $pdfPath   = $bookFile->path;
                $fileName .= '_' . $bookFile->language;
            }
        } else {
            $pdfPath = $book->pdf_file;
        }

        if (!$pdfPath) {
            abort(404, __('Fichier PDF introuvable.'));
        }

        if (Storage::disk('public')->exists($pdfPath)) {
            return Storage::disk('public')->download($pdfPath, $fileName . '.pdf');
        } elseif (Storage::exists($pdfPath)) {
            return Storage::download($pdfPath, $fileName . '.pdf');
        }

        abort(404, __('Fichier introuvable sur le serveur.'));
    }
}