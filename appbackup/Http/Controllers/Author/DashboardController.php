<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Book;
use App\Models\Purchase;
use App\Models\ReadingProgress;
use App\Models\AudioProgress;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $author = auth()->user();

        // Key Metrics
        $totalBooks = $author->books()->count();
        $totalRevenue = $author->revenues()->sum('author_amount');
        $totalReviews = Review::whereIn('book_id', $author->books()->pluck('id'))->count();
        $lifetimeSales = $author->revenues()->whereIn('revenue_type', ['pdf_sale', 'audio_sale'])->distinct('payment_id')->count();


        // Recently Published Books
        $recentlyPublishedBooks = $author->books()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest('created_at')
            ->take(3)
            ->get();

        // Recent Reviews
        $recentReviews = Review::whereIn('book_id', $author->books()->pluck('id'))
            ->with('book', 'user')
            ->latest()
            ->take(5)
            ->get();

        // Revenue Chart Data (last 6 months)
        $revenueByMonth = $author->revenues()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, sum(author_amount) as total")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->pluck('total', 'month');

        $chartLabels = collect();
        $chartData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $chartLabels->push($month->format('M'));
            $chartData->push($revenueByMonth->get($monthKey, 0));
        }

        $revenueChart = [
            'labels' => $chartLabels,
            'data' => $chartData,
        ];

        return view('author.dashboard.index', compact(
            'totalBooks',
            'totalRevenue',
            'totalReviews',
            'lifetimeSales',
            'recentlyPublishedBooks',
            'recentReviews',
            'revenueChart'
        ));
    }

    public function statistics()
    {
        $author = auth()->user();
        $authorBookIds = $author->books()->pluck('id');

        // 1. Per-Book Statistics with pagination
        $books = $author->books()
            ->withCount(['reviews', 'purchases as sales_count'])
            ->withSum('readingProgress as total_time_read_seconds', 'time_spent')
            ->withSum('audioProgress as total_time_listened_seconds', 'current_position')
            ->withAvg('reviews as avg_rating', 'rating')
            ->paginate(10);

        // 2. Global Author Statistics
        $globalStats = [
            'total_sales' => Purchase::whereIn('book_id', $authorBookIds)->count(),
            'total_revenue' => $author->revenues()->sum('author_amount'),
            'total_reviews' => Review::whereIn('book_id', $authorBookIds)->count(),
            'overall_avg_rating' => Review::whereIn('book_id', $authorBookIds)->avg('rating'),
            'total_reading_seconds' => ReadingProgress::whereIn('book_id', $authorBookIds)->sum('time_spent'),
            'total_listening_seconds' => AudioProgress::whereIn('book_id', $authorBookIds)->sum('current_position'),
        ];
        
        // 3. Data for Charts
        $revenueByMonth = $author->revenues()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, sum(author_amount) as total")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()->pluck('total', 'month');

        $salesByMonth = Purchase::whereIn('book_id', $authorBookIds)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as total")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()->pluck('total', 'month');

        $chartLabels = collect();
        $revenueData = collect();
        $salesData = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $chartLabels->push($month->format('M Y'));
            $revenueData->push($revenueByMonth->get($monthKey, 0));
            $salesData->push($salesByMonth->get($monthKey, 0));
        }

        $chartData = [
            'labels' => $chartLabels,
            'revenue' => $revenueData,
            'sales' => $salesData,
        ];

        // 4. Top 5 Lists
        $topSellingBooks = Book::whereIn('id', $authorBookIds)
            ->withCount('purchases')
            ->orderBy('purchases_count', 'desc')
            ->take(5)->get();
        
        $topReadBooks = Book::whereIn('id', $authorBookIds)
            ->withSum('readingProgress as total_read', 'time_spent')
            ->orderBy('total_read', 'desc')
            ->take(5)->get();

        return view('author.dashboard.statistics', compact('books', 'globalStats', 'chartData', 'topSellingBooks', 'topReadBooks'));
    }

    public function reviews(Request $request)
    {
        $author = auth()->user();
        $search = $request->input('search');

        $reviewsQuery = Review::whereIn('book_id', $author->books()->pluck('id'))
            ->with('book', 'user');

        if ($search) {
            $reviewsQuery->where(function ($query) use ($search) {
                $query->where('comment', 'like', "%{$search}%")
                    ->orWhereHas('book', function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }
        
        $reviews = $reviewsQuery->latest()->paginate(10)->withQueryString();

        return view('author.dashboard.reviews', compact('reviews', 'search'));
    }


    public function showReview(Review $review)
    {
        $author = auth()->user();
        $bookIds = $author->books()->pluck('id');

        if ($bookIds->contains($review->book_id)) {
            return view('author.dashboard.review', compact('review'));
        }

        abort(403);
    }

    public function profile()
    {
        $author = auth()->user();

        return view('author.dashboard.profile', compact('author'));
    }

    public function updateProfile(Request $request)
    {
        $author = auth()->user();

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$author->id,
            'phone' => 'nullable|string|max:255',
        ]);

        $author->update($request->only('first_name', 'last_name', 'email', 'phone'));

        return back()->with('success', 'Profile updated successfully.');
    }
}
