<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthorPayout;
use App\Models\Revenue;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class RevenueManagementController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        // Get current tab from request, default to 'pending'
        $currentTab = $request->input('tab', 'pending');

        // Calculate statistics
        $stats = [
            'total' => Revenue::sum('total_amount'),
            'total_current_month' => Revenue::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total_amount'),
            'pending_count' => Revenue::where('status', 'pending')->count(),
            'total_payouts' => AuthorPayout::where('status', 'completed')->sum('amount'),
        ];

        // Query revenues based on the current tab
        $revenuesQuery = Revenue::with('author', 'book');

        switch ($currentTab) {
            case 'approved':
                $revenuesQuery->where('status', 'approved');
                break;
            case 'paid':
                $revenuesQuery->where('status', 'paid');
                break;
            case 'all':
                // No status filter needed
                break;
            case 'pending':
            default:
                $revenuesQuery->where('status', 'pending');
                break;
        }

        $revenues = $revenuesQuery->latest()->paginate(15)->withQueryString();

        return view('admin.revenues.index', compact('revenues', 'stats', 'currentTab'));
    }

    public function authors()
    {
        $authors = User::where('role', 'author')
            ->withSum('revenues', 'author_amount')
            ->paginate(10);

        return view('admin.revenues.authors', compact('authors'));
    }

    public function authorDetail(User $author)
    {
        $revenues = $author->revenues()->with('book')->paginate(10);
        $totalEarnings = $author->revenues()->sum('author_amount');

        return view('admin.revenues.author-detail', compact('author', 'revenues', 'totalEarnings'));
    }

    public function approvePeriod(Request $request)
    {
        // Logic to approve a revenue period, e.g., mark all revenues within a period as 'approved'
        // This is a placeholder for a more complex business logic
        return back()->with('success', 'Revenue period approved (placeholder).');
    }

    public function distributeSubscriptions()
    {
        // Complex logic to calculate and distribute subscription revenue to authors
        // This is a placeholder for a more complex business logic
        return back()->with('success', 'Subscription revenue distributed (placeholder).');
    }

    public function payouts()
    {
        $payouts = AuthorPayout::with('author')->paginate(10);

        return view('admin.revenues.payouts.index', compact('payouts'));
    }

    public function createPayout(User $author)
    {
        return view('admin.revenues.payouts.create', compact('author'));
    }

    public function storePayout(Request $request)
    {
        $request->validate([
            'author_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|max:3',
            'payment_method' => 'required|string|in:mobile_money,bank_transfer',
            'payment_details' => 'required|string',
            'notes' => 'nullable|string',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        AuthorPayout::create(array_merge($request->all(), ['status' => 'pending']));

        return redirect()->route('admin.revenues.payouts.index')->with('success', 'Payout created successfully.');
    }

    public function showPayout(AuthorPayout $payout)
    {
        return view('admin.revenues.payouts.show', compact('payout'));
    }

    public function confirmPayout(AuthorPayout $payout)
    {
        $payout->update(['status' => 'completed', 'processed_at' => now()]);

        // Notify the author
        if ($payout->author) {
            $this->notificationService->sendNotification(
                $payout->author,
                'Paiement de revenus effectué',
                "Votre paiement de revenus d'un montant de {$payout->amount} {$payout->currency} a été traité avec succès.",
                route('author.revenues'), // Assuming this route exists for authors
                'success'
            );
        }

        return back()->with('success', 'Payout confirmed successfully.');
    }

    public function cancelPayout(AuthorPayout $payout)
    {
        $payout->update(['status' => 'cancelled']);

        return back()->with('success', 'Payout cancelled successfully.');
    }

    /**
     * Show the form for editing the specified revenue.
     */
    public function edit(Revenue $revenue)
    {
        return view('admin.revenues.edit', compact('revenue'));
    }

    /**
     * Update the specified revenue in storage.
     */
    public function update(Request $request, Revenue $revenue)
    {
        $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'author_amount' => 'required|numeric|min:0',
            'platform_amount' => 'required|numeric|min:0',
            'author_percentage' => 'required|integer|min:0|max:100',
            'status' => 'required|in:pending,approved,paid',
        ]);

        $revenue->update($request->all());

        return redirect()->route('admin.revenues.index')->with('success', 'Revenue record updated successfully.');
    }

    /**
     * Approve the specified revenue.
     */
    public function approve(Revenue $revenue)
    {
        if ($revenue->status === 'pending') {
            $revenue->update(['status' => 'approved']);

            return back()->with('success', 'Revenue approved successfully.');
        }

        return back()->with('info', 'Revenue was not in pending state.');
    }

    /**
     * Remove the specified revenue from storage.
     */
    public function destroy(Revenue $revenue)
    {
        $revenue->delete();

        return back()->with('success', 'Revenue record deleted successfully.');
    }
}
