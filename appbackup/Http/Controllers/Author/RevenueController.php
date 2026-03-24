<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\AuthorPayout;
use App\Models\Revenue;
use App\Models\Setting;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $author = auth()->user();
        $search = $request->input('search');

        $revenuesQuery = $author->revenues()->with('book');

        if ($search) {
            $revenuesQuery->where(function ($query) use ($search) {
                $query->where('revenue_type', 'like', '%'.$search.'%')
                    ->orWhere('status', 'like', '%'.$search.'%')
                    ->orWhereHas('book', function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%");
                    });
            });
        }

        $revenues = $revenuesQuery->latest()->paginate(10)->withQueryString();
        
        $totalEarnings = $author->revenues()->sum('author_amount');
        $totalPaid = $author->revenues()->whereNotNull('paid_at')->sum('author_amount');
        $totalUnpaid = $author->revenues()->where('status', 'approved')->whereNull('paid_at')->sum('author_amount');


        return view('author.revenues.index', compact('revenues', 'totalEarnings', 'totalPaid', 'totalUnpaid', 'search'));
    }

    public function details()
    {
        $author = auth()->user();
        $revenues = $author->revenues()->with('book', 'payment')->paginate(10);

        return view('author.revenues.details', compact('revenues'));
    }

    public function history()
    {
        $author = auth()->user();
        $payouts = $author->payouts()->paginate(10);

        return view('author.revenues.history', compact('payouts'));
    }

    public function monthlyReport(string $year, string $month)
    {
        $author = auth()->user();
        $revenues = $author->revenues()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();
        $totalMonthlyRevenue = $revenues->sum('author_amount');

        return view('author.revenues.monthly-report', compact('revenues', 'totalMonthlyRevenue', 'year', 'month'));
    }

    public function showPayoutRequestForm()
    {
        $author = auth()->user();

        $totalApprovedUnpaidRevenue = Revenue::where('author_id', $author->id)
            ->where('status', 'approved')
            ->whereNull('paid_at')
            ->sum('author_amount');

        $totalPendingPayoutsAmount = AuthorPayout::where('author_id', $author->id)
            ->where('status', 'pending')
            ->sum('amount');

        $availableBalance = $totalApprovedUnpaidRevenue - $totalPendingPayoutsAmount;

        $pendingPayout = AuthorPayout::where('author_id', $author->id)
            ->where('status', 'pending')
            ->first();

        $minimumPayout = Setting::where('key', 'platform.minimum_payout')->value('value') ?? 5000;

        return view('author.revenues.payout-request', compact('availableBalance', 'pendingPayout', 'minimumPayout'));
    }

    public function submitPayoutRequest(Request $request)
    {
        $author = auth()->user();

        $request->validate([
            'payment_method' => 'required|string|in:mobile_money,bank_transfer',
            'payment_details' => 'required|string|max:255',
        ]);

        $allApprovedUnpaidRevenues = Revenue::where('author_id', $author->id)
            ->where('status', 'approved')
            ->whereNull('paid_at')
            ->get();

        $totalApprovedUnpaidRevenue = $allApprovedUnpaidRevenues->sum('author_amount');

        $totalPendingPayoutsAmount = AuthorPayout::where('author_id', $author->id)
            ->where('status', 'pending')
            ->sum('amount');

        $availableBalance = $totalApprovedUnpaidRevenue - $totalPendingPayoutsAmount;

        $minimumPayout = Setting::where('key', 'platform.minimum_payout')->value('value') ?? 5000;

        if ($availableBalance < $minimumPayout) {
            return redirect()->back()->with('error', 'Le montant minimum pour un versement est de '.$minimumPayout.' FCFA.');
        }

        $hasPendingPayout = AuthorPayout::where('author_id', $author->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingPayout) {
            return redirect()->back()->with('error', 'Vous avez déjà une demande de versement en attente.');
        }

        // Determine the period from the revenues that are currently available for payout
        $periodStart = $allApprovedUnpaidRevenues->min('created_at');
        $periodEnd = $allApprovedUnpaidRevenues->max('created_at');

        AuthorPayout::create([
            'author_id' => $author->id,
            'amount' => $availableBalance,
            'payment_method' => $request->payment_method,
            'payment_details' => $request->payment_details,
            'status' => 'pending',
            'payout_reference' => 'REQ-'.strtoupper(uniqid()),
            'currency' => 'XOF',
            'period_start' => $periodStart ?? now(),
            'period_end' => $periodEnd ?? now(),
        ]);

        return redirect()->route('author.revenues.history')->with('success', 'Votre demande de versement a été envoyée avec succès.');
    }
}
