<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdultAccess;
use App\Models\AuthorPayout;
use App\Models\Book;
use App\Models\Payment;
use App\Models\Review;
use App\Models\School;
use App\Models\User;
use App\Models\Subscription;

class DashboardController extends Controller
{
    public function index()
    {
        // --- Key Metrics ---
        $totalUsers = User::count();
        $totalAuthors = User::where('role', 'author')->count();
        $totalSchools = School::count();
        $totalBooks = Book::count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        
        // --- Financials ---
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $monthlyRevenue = Payment::where('status', 'completed')->where('created_at', '>=', now()->startOfMonth())->sum('amount');
        $annualRevenue = Payment::where('status', 'completed')->where('created_at', '>=', now()->startOfYear())->sum('amount');

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

        return view('admin.dashboard.index', compact(
            'totalUsers', 'totalAuthors', 'totalSchools', 'totalBooks', 'activeSubscriptions',
            'totalRevenue', 'monthlyRevenue', 'annualRevenue',
            'pendingBooks', 'pendingReviews', 'pendingPayouts',
            'revenueChart', 'userChart',
            'latestUsers', 'latestBooks', 'latestReviews'
        ));
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
