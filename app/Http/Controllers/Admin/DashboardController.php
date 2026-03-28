<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthorPayout;
use App\Models\Book;
use App\Models\Payment;
use App\Models\Review;
use App\Models\School;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Paiements validés : complétés par l’admin (ou le PSP) avec date d’encaissement.
     * Exclut les paiements encore en attente (pending) ou sans paid_at.
     */
    protected function approvedPaymentsQuery(): Builder
    {
        return Payment::query()
            ->where('status', 'completed')
            ->whereNotNull('paid_at');
    }

    public function index()
    {
        // --- Key Metrics ---
        $totalUsers = User::count();
        $totalAuthors = User::where('role', 'author')->count();
        $totalSchools = School::count();
        $totalBooks = Book::count();
        $publishedBooks = Book::where('status', 'published')->count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();

        $monthStart = now()->startOfMonth();

        // --- Financials (uniquement paiements validés : completed + paid_at) ---
        $totalRevenue = (clone $this->approvedPaymentsQuery())->sum('amount');
        $monthlyRevenue = (clone $this->approvedPaymentsQuery())
            ->where('paid_at', '>=', $monthStart)
            ->sum('amount');
        $annualRevenue = (clone $this->approvedPaymentsQuery())
            ->where('paid_at', '>=', now()->startOfYear())
            ->sum('amount');

        $paymentsCountMonth = (clone $this->approvedPaymentsQuery())
            ->where('paid_at', '>=', $monthStart)
            ->count();
        $avgOrderValueMonth = $paymentsCountMonth > 0 ? round((float) $monthlyRevenue / $paymentsCountMonth, 2) : 0;

        $subscriptionRevenueMonth = (clone $this->approvedPaymentsQuery())
            ->where('paid_at', '>=', $monthStart)
            ->where('payment_type', 'subscription')
            ->sum('amount');
        $directSalesRevenueMonth = (clone $this->approvedPaymentsQuery())
            ->where('paid_at', '>=', $monthStart)
            ->whereIn('payment_type', ['book_pdf', 'book_audio'])
            ->sum('amount');

        // --- Growth & pipeline ---
        $newUsersThisMonth = User::where('created_at', '>=', $monthStart)->count();
        $pendingRevenueAmount = (float) DB::table('revenues')->where('status', 'pending')->sum('total_amount');

        // --- Top authors (volume = somme des montants attribués sur leurs livres) ---
        $topAuthors = $this->buildTopAuthors();

        // --- Top books (même règle : paiement validé + revenu approuvé ou payé) ---
        $topBooks = $this->validatedRevenuesQuery()
            ->join('books', 'revenues.book_id', '=', 'books.id')
            ->join('users as authors', 'books.author_id', '=', 'authors.id')
            ->select(
                'books.id',
                'books.title',
                'books.slug',
                DB::raw('COALESCE(authors.first_name, "") as author_first'),
                DB::raw('COALESCE(authors.last_name, "") as author_last'),
                DB::raw('SUM(revenues.total_amount) as volume'),
                DB::raw('COUNT(revenues.id) as allocations')
            )
            ->groupBy('books.id', 'books.title', 'books.slug', 'authors.first_name', 'authors.last_name')
            ->orderByDesc('volume')
            ->limit(5)
            ->get();

        // --- Pending Items ---
        $pendingBooks = Book::where('status', 'pending')->count();
        $pendingReviews = Review::where('is_approved', '0')->count();
        $pendingPayouts = AuthorPayout::where('status', 'pending')->count();

        // --- Chart Data (Last 12 months) — date de validation = paid_at ---
        $revenueByMonth = (clone $this->approvedPaymentsQuery())
            ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as month, sum(amount) as total")
            ->where('paid_at', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->pluck('total', 'month');

        $revenueChartLabels = collect();
        $revenueChartData = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $revenueChartLabels->push($month->format('M Y'));
            $revenueChartData->push($revenueByMonth->get($monthKey, 0));
        }
        $revenueChart = ['labels' => $revenueChartLabels, 'data' => $revenueChartData];

        // Users Chart
        $usersByMonth = User::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()->pluck('count', 'month');
        
        $userChartLabels = collect();
        $userChartData = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $userChartLabels->push($month->format('M Y'));
            $userChartData->push($usersByMonth->get($monthKey, 0));
        }
        $userChart = ['labels' => $userChartLabels, 'data' => $userChartData];


        // --- Latest Activity ---
        $latestUsers = User::latest()->take(5)->get();
        $latestBooks = Book::with('author')->latest()->take(5)->get();
        $latestReviews = Review::with('user', 'book')->latest()->take(5)->get();

        $directShareMonth = (float) $monthlyRevenue > 0
            ? round(((float) $directSalesRevenueMonth / (float) $monthlyRevenue) * 100, 1)
            : 0;
        $subscriptionShareMonth = (float) $monthlyRevenue > 0
            ? round(((float) $subscriptionRevenueMonth / (float) $monthlyRevenue) * 100, 1)
            : 0;

        return view('admin.dashboard.index', compact(
            'totalUsers', 'totalAuthors', 'totalSchools', 'totalBooks', 'publishedBooks', 'activeSubscriptions',
            'totalRevenue', 'monthlyRevenue', 'annualRevenue',
            'paymentsCountMonth', 'avgOrderValueMonth',
            'subscriptionRevenueMonth', 'directSalesRevenueMonth', 'directShareMonth', 'subscriptionShareMonth',
            'newUsersThisMonth', 'pendingRevenueAmount',
            'topAuthors', 'topBooks',
            'pendingBooks', 'pendingReviews', 'pendingPayouts',
            'revenueChart', 'userChart',
            'latestUsers', 'latestBooks', 'latestReviews'
        ));
    }

    /**
     * @return Collection<int, object{author: User, volume: float|int, author_earnings: float|int, allocations: int}>
     */
    protected function buildTopAuthors(): Collection
    {
        $rows = DB::table('revenues')
            ->join('payments', 'revenues.payment_id', '=', 'payments.id')
            ->where('payments.status', 'completed')
            ->whereNotNull('payments.paid_at')
            ->whereIn('revenues.status', ['approved', 'paid'])
            ->select(
                'revenues.author_id',
                DB::raw('SUM(revenues.total_amount) as volume'),
                DB::raw('SUM(revenues.author_amount) as author_earnings'),
                DB::raw('COUNT(revenues.id) as allocations')
            )
            ->groupBy('revenues.author_id')
            ->orderByDesc('volume')
            ->limit(5)
            ->get();

        if ($rows->isEmpty()) {
            return collect();
        }

        $authors = User::whereIn('id', $rows->pluck('author_id'))->get()->keyBy('id');

        return $rows->map(function ($row) use ($authors) {
            return (object) [
                'author' => $authors->get($row->author_id),
                'volume' => (float) $row->volume,
                'author_earnings' => (float) $row->author_earnings,
                'allocations' => (int) $row->allocations,
            ];
        })->filter(fn ($item) => $item->author !== null)->values();
    }

    /**
     * Base query for revenue rows included in rankings (validated payment + revenue approuvé ou payé).
     */
    protected function validatedRevenuesQuery(): \Illuminate\Database\Query\Builder
    {
        return DB::table('revenues')
            ->join('payments', 'revenues.payment_id', '=', 'payments.id')
            ->where('payments.status', 'completed')
            ->whereNotNull('payments.paid_at')
            ->whereIn('revenues.status', ['approved', 'paid']);
    }

    public function statistics()
    {
        $months = 24;
        $periodStart = now()->subMonths($months - 1)->startOfMonth();

        $usersByMonthKey = User::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as cnt')
            ->where('created_at', '>=', $periodStart)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $booksByMonthKey = Book::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as cnt')
            ->where('created_at', '>=', $periodStart)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $revenueByMonthKey = (clone $this->approvedPaymentsQuery())
            ->selectRaw('DATE_FORMAT(paid_at, "%Y-%m") as month, SUM(amount) as total_amount')
            ->where('paid_at', '>=', $periodStart)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $chartLabels = collect();
        $userSeries = collect();
        $bookSeries = collect();
        $revenueSeries = collect();

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $key = $month->format('Y-m');
            $chartLabels->push($month->locale(app()->getLocale())->translatedFormat('M Y'));

            $userSeries->push((int) (optional($usersByMonthKey->get($key))->cnt ?? 0));
            $bookSeries->push((int) (optional($booksByMonthKey->get($key))->cnt ?? 0));
            $revenueSeries->push((float) (optional($revenueByMonthKey->get($key))->total_amount ?? 0));
        }

        $summary = [
            'users_total' => User::count(),
            'books_total' => Book::count(),
            'books_published' => Book::where('status', 'published')->count(),
            'revenue_total' => (float) (clone $this->approvedPaymentsQuery())->sum('amount'),
            'revenue_year' => (float) (clone $this->approvedPaymentsQuery())
                ->where('paid_at', '>=', now()->startOfYear())
                ->sum('amount'),
            'payments_validated' => (clone $this->approvedPaymentsQuery())->count(),
        ];

        return view('admin.dashboard.statistics', [
            'chartLabels' => $chartLabels,
            'userSeries' => $userSeries,
            'bookSeries' => $bookSeries,
            'revenueSeries' => $revenueSeries,
            'summary' => $summary,
            'monthsWindow' => $months,
        ]);
    }

    public function activityReport()
    {
        $recentUsers = User::latest()->take(10)->get();
        $recentBooks = Book::latest()->take(10)->get();
        $recentReviews = Review::latest()->take(10)->get();

        return view('admin.dashboard.activity-report', compact('recentUsers', 'recentBooks', 'recentReviews'));
    }

    public function export(string $type)
    {
        // In a real application, this would generate and download a file (e.g., CSV, Excel)
        return response('Exporting '.$type.' data...')->header('Content-Type', 'text/plain');
    }
}
