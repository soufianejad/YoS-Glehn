<?php

namespace App\Services;

use App\Models\AuthorPayout;
use App\Models\Book;
use App\Models\Payment;
use App\Models\Revenue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Revenue Calculator Service
 * Gère le calcul et la répartition des revenus entre auteurs et plateforme
 */
class RevenueCalculatorService
{
    /**
     * Enregistrer un revenu suite à un paiement
     */
    public function recordRevenue(Payment $payment): ?Revenue
    {
        try {
            // Find the book using its ID from the payment object.
            // This is more robust than using the relationship, especially for in-memory objects.
            $book = Book::find($payment->book_id);

            if (! $book) {
                Log::warning('Revenue recording failed: Book not found for book_id', ['book_id' => $payment->book_id]);

                return null;
            }

            // Calculate amounts based on the book's revenue share percentages
            $authorPercentage = $book->author_revenue_percentage;
            $platformPercentage = $book->platform_revenue_percentage;

            $totalAmount = $payment->amount;
            $authorAmount = $totalAmount * ($authorPercentage / 100);
            $platformAmount = $totalAmount * ($platformPercentage / 100);

            // Create the revenue record
            $revenue = Revenue::create([
                'author_id' => $book->author_id,
                'book_id' => $book->id,
                'payment_id' => $payment->id, // This can be the original subscription payment ID
                'total_amount' => $totalAmount,
                'author_amount' => $authorAmount,
                'platform_amount' => $platformAmount,
                'author_percentage' => $authorPercentage,
                'revenue_type' => $this->determineRevenueType($payment),
                'status' => 'pending', // Pending validation
            ]);

            return $revenue;

        } catch (\Exception $e) {
            Log::error('Error recording revenue: '.$e->getMessage(), ['payment_id' => $payment->id, 'book_id' => $payment->book_id]);

            return null;
        }
    }

    /**
     * Déterminer le type de revenu en fonction du paiement
     */
    protected function determineRevenueType(Payment $payment): string
    {
        switch ($payment->payment_type) {
            case 'subscription':
                return 'subscription';
            case 'book_pdf':
                return 'pdf_sale';
            case 'book_audio':
                return 'audio_sale';
            default:
                return 'subscription';
        }
    }

    /**
     * Calculer les revenus d'un auteur pour une période donnée
     */
    public function calculateAuthorRevenues(User $author, Carbon $startDate, Carbon $endDate): array
    {
        $revenues = Revenue::where('author_id', $author->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return [
            'total_revenue' => $revenues->sum('author_amount'),
            'pending_revenue' => $revenues->where('status', 'pending')->sum('author_amount'),
            'approved_revenue' => $revenues->where('status', 'approved')->sum('author_amount'),
            'paid_revenue' => $revenues->where('status', 'paid')->sum('author_amount'),
            'by_type' => [
                'subscription' => $revenues->where('revenue_type', 'subscription')->sum('author_amount'),
                'pdf_sale' => $revenues->where('revenue_type', 'pdf_sale')->sum('author_amount'),
                'audio_sale' => $revenues->where('revenue_type', 'audio_sale')->sum('author_amount'),
            ],
            'by_book' => $revenues->groupBy('book_id')->map(function ($bookRevenues) {
                return [
                    'book_id' => $bookRevenues->first()->book_id,
                    'book_title' => $bookRevenues->first()->book->title,
                    'amount' => $bookRevenues->sum('author_amount'),
                ];
            })->values(),
        ];
    }

    /**
     * Approuver les revenus en attente pour un auteur
     *
     * @return int Nombre de revenus approuvés
     */
    public function approveAuthorRevenues(User $author, Carbon $startDate, Carbon $endDate): int
    {
        return Revenue::where('author_id', $author->id)
            ->where('status', 'pending')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->update(['status' => 'approved']);
    }

    /**
     * Créer un versement pour un auteur
     */
    public function createAuthorPayout(
        User $author,
        Carbon $periodStart,
        Carbon $periodEnd,
        string $paymentMethod,
        string $paymentDetails
    ): ?AuthorPayout {
        try {
            DB::beginTransaction();

            // Calculer le montant total à verser (revenus approuvés)
            $approvedRevenues = Revenue::where('author_id', $author->id)
                ->where('status', 'approved')
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->get();

            $totalAmount = $approvedRevenues->sum('author_amount');

            if ($totalAmount <= 0) {
                DB::rollBack();

                return null;
            }

            // Créer le versement
            $payout = AuthorPayout::create([
                'author_id' => $author->id,
                'payout_reference' => $this->generatePayoutReference(),
                'amount' => $totalAmount,
                'currency' => 'XOF',
                'payment_method' => $paymentMethod,
                'payment_details' => $paymentDetails,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'status' => 'pending',
            ]);

            // Marquer les revenus comme "payés"
            Revenue::whereIn('id', $approvedRevenues->pluck('id'))
                ->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

            DB::commit();

            return $payout;

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur création versement: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Générer une référence unique pour un versement
     */
    protected function generatePayoutReference(): string
    {
        return 'PAYOUT-'.strtoupper(uniqid()).'-'.time();
    }

    /**
     * Confirmer un versement
     */
    public function confirmPayout(AuthorPayout $payout): bool
    {
        return $payout->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);
    }

    /**
     * Calculer les revenus de la plateforme pour une période
     */
    public function calculatePlatformRevenues(Carbon $startDate, Carbon $endDate): array
    {
        $revenues = Revenue::whereBetween('created_at', [$startDate, $endDate])->get();
        $payments = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return [
            'total_payments' => $payments->sum('amount'),
            'platform_share' => $revenues->sum('platform_amount'),
            'author_share' => $revenues->sum('author_amount'),
            'by_payment_type' => [
                'subscriptions' => $payments->where('payment_type', 'subscription')->sum('amount'),
                'pdf_sales' => $payments->where('payment_type', 'book_pdf')->sum('amount'),
                'audio_sales' => $payments->where('payment_type', 'book_audio')->sum('amount'),
            ],
            'pending_author_payments' => Revenue::where('status', 'approved')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('author_amount'),
        ];
    }

    /**
     * Obtenir le rapport de revenus par auteur
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAuthorRevenueReport(Carbon $startDate, Carbon $endDate)
    {
        return User::where('role', 'author')
            ->withCount('books')
            ->with(['revenues' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function ($author) {
                $revenues = $author->revenues;

                return [
                    'author_id' => $author->id,
                    'author_name' => $author->full_name,
                    'books_count' => $author->books_count,
                    'total_revenue' => $revenues->sum('author_amount'),
                    'pending' => $revenues->where('status', 'pending')->sum('author_amount'),
                    'approved' => $revenues->where('status', 'approved')->sum('author_amount'),
                    'paid' => $revenues->where('status', 'paid')->sum('author_amount'),
                ];
            })
            ->sortByDesc('total_revenue')
            ->values();
    }

    /**
     * Distribuer automatiquement les revenus des abonnements
     * Appelé périodiquement (cron job) pour répartir les revenus d'abonnement
     *
     * @param  Carbon  $month  Mois concerné
     * @return array Résumé de la distribution
     */
    public function distributeSubscriptionRevenues(Carbon $month): array
    {
        try {
            DB::beginTransaction();

            $startDate = $month->copy()->startOfMonth();
            $endDate = $month->copy()->endOfMonth();

            \Log::info('--- Début distribution revenus abonnements ---', [
                'month' => $month->format('Y-m'),
                'startDate' => $startDate->toDateString(),
                'endDate' => $endDate->toDateString(),
            ]);

            // Récupérer tous les paiements d'abonnement du mois
            $subscriptionPayments = Payment::where('payment_type', 'subscription')
                ->where('status', 'completed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereDoesntHave('revenues') // Pas encore distribués
                ->get();

            \Log::info('Paiements trouvés', ['count' => $subscriptionPayments->count()]);

            $distributed = 0;
            $totalAmount = 0;

            foreach ($subscriptionPayments as $payment) {
                \Log::info('Traitement du paiement', [
                    'payment_id' => $payment->id,
                    'amount' => $payment->amount,
                    'user_id' => optional($payment->subscription)->user_id,
                ]);

                $subscription = $payment->subscription;
                if (! $subscription) {
                    \Log::warning('Aucun abonnement trouvé pour ce paiement', ['payment_id' => $payment->id]);

                    continue;
                }

                $user = $subscription->user;

                $consumedBooks = $this->getUserConsumedBooks($user, $startDate, $endDate);

                if ($consumedBooks->isEmpty()) {
                    \Log::info('Aucun livre consommé pour cet utilisateur durant la période', [
                        'user_id' => $user->id,
                        'payment_id' => $payment->id,
                    ]);

                    continue;
                }

                $amountPerBook = $payment->amount / $consumedBooks->count();

                \Log::info('Répartition du paiement sur les livres', [
                    'user_id' => $user->id,
                    'books_count' => $consumedBooks->count(),
                    'amount_per_book' => $amountPerBook,
                ]);

                foreach ($consumedBooks as $book) {

                    // Create a temporary payment object for revenue calculation
                    // We set properties manually to ensure the ID is passed correctly,
                    // avoiding mass assignment issues.
                    $revenueData = new Payment;
                    $revenueData->id = $payment->id; // Original subscription payment ID
                    $revenueData->amount = $amountPerBook;
                    $revenueData->book_id = $book->id;
                    $revenueData->payment_type = 'subscription';

                    $this->recordRevenue($revenueData);

                    Log::debug('Revenu enregistré', [
                        'book_id' => $book->id,
                        'amount' => $amountPerBook,
                    ]);

                    $distributed++;
                    $totalAmount += $amountPerBook;
                }
            }

            DB::commit();

            \Log::info('--- Distribution terminée ---', [
                'payments_processed' => $subscriptionPayments->count(),
                'revenues_distributed' => $distributed,
                'total_amount' => $totalAmount,
            ]);

            return [
                'success' => true,
                'payments_processed' => $subscriptionPayments->count(),
                'revenues_distributed' => $distributed,
                'total_amount' => $totalAmount,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur distribution revenus : '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Obtenir les livres consommés par un utilisateur pendant une période
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getUserConsumedBooks(User $user, Carbon $startDate, Carbon $endDate)
    {
        // Livres lus (PDF)
        $readBooks = $user->readingProgress()
            ->whereBetween('last_read_at', [$startDate, $endDate])
            ->where('progress_percentage', '>', 0.1) // Au moins 10% lu
            ->pluck('book_id');

        // Livres écoutés (Audio)
        $listenedBooks = $user->audioProgress()
            ->whereBetween('last_listened_at', [$startDate, $endDate])
            ->where('progress_percentage', '>', 0.1) // Au moins 10% écouté
            ->pluck('book_id');

        // Fusionner et supprimer les doublons
        $bookIds = $readBooks->merge($listenedBooks)->unique();

        return Book::whereIn('id', $bookIds)->get();
    }
}
