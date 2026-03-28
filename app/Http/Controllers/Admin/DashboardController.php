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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // --- Key Metrics ---
        $totalUsers = User::count();
        $totalAuthors = User::where('role', 'author')->count();
        $totalSchools = School::count();
        $totalBooks = Book::count();
        $publishedBooks = Book::where('status', 'published')->count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();

        // --- Financials ---
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $monthStart = now()->startOfMonth();
        $monthlyRevenue = Payment::where('status', 'completed')->where('created_at', '>=', $monthStart)->sum('amount');
        $annualRevenue = Payment::where('status', 'completed')->where('created_at', '>=', now()->startOfYear())->sum('amount');

        $paymentsCountMonth = Payment::where('status', 'completed')->where('created_at', '>=', $monthStart)->count();
        $avgOrderValueMonth = $paymentsCountMonth > 0 ? round((float) $monthlyRevenue / $paymentsCountMonth, 2) : 0;

        $subscriptionRevenueMonth = Payment::where('status', 'completed')
            ->where('created_at', '>=', $monthStart)
            ->where('payment_type', 'subscription')
            ->sum('amount');
        $directSalesRevenueMonth = Payment::where('status', 'completed')
            ->where('created_at', '>=', $monthStart)
            ->whereIn('payment_type', ['book_pdf', 'book_audio'])
            ->sum('amount');

        // --- Growth & pipeline ---
        $newUsersThisMonth = User::where('created_at', '>=', $monthStart)->count();
        $pendingRevenueAmount = (float) DB::table('revenues')->where('status', 'pending')->sum('total_amount');

        // --- Top authors (volume = somme des montants attribués sur leurs livres) ---
        $topAuthors = $this->buildTopAuthors();

        // --- Top books by attributed revenue ---
        $topBooks = DB::table('revenues')
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

        // --- Chart Data (Last 12 months) ---
        // Revenue Chart
        $revenueByMonth = Payment::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, sum(amount) as total")
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subYear())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()->pluck('total', 'month');

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
            ->select(
                'author_id',
                DB::raw('SUM(total_amount) as volume'),
                DB::raw('SUM(author_amount) as author_earnings'),
                DB::raw('COUNT(*) as allocations')
            )
            ->groupBy('author_id')
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

    public function statistics()
    {
        // Placeholder for more detailed statistics
        $usersByMonth = User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $booksByMonth = Book::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $revenueByMonth = Payment::selectRaw('DATE_FORMAT(paid_at, "%Y-%m") as month, sum(amount) as total_amount')
            ->where('status', 'completed')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard.statistics', compact('usersByMonth', 'booksByMonth', 'revenueByMonth'));
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
