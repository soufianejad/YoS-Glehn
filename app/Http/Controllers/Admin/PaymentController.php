<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Purchase;
use App\Services\RevenueCalculatorService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $revenueCalculator;
    protected $paymentService;

    public function __construct(RevenueCalculatorService $revenueCalculator, PaymentService $paymentService)
    {
        $this->revenueCalculator = $revenueCalculator;
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        // 1. Get filters from request
        $currentTab = $request->input('tab', 'all');
        $search = $request->input('search');

        // 2. Calculate statistics
        $stats = [
            'total_count' => Payment::count(),
            'total_amount' => Payment::where('status', 'completed')->sum('amount'),
            'today_count' => Payment::whereDate('created_at', today())->count(),
            'today_amount' => Payment::where('status', 'completed')->whereDate('created_at', today())->sum('amount'),
        ];

        // 3. Base query
        $paymentsQuery = Payment::with('user', 'book', 'subscription');

        // 4. Apply search filter
        if ($search) {
            $paymentsQuery->where(function($query) use ($search) {
                $query->where('transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function($q) use ($search) {
                        $q->where('email', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // 5. Apply tab filter
        if (in_array($currentTab, ['completed', 'pending', 'failed', 'refunded'])) {
            $paymentsQuery->where('status', $currentTab);
        }

        $payments = $paymentsQuery->latest()->paginate(15)->withQueryString();

        return view('admin.payments.index', compact('payments', 'stats', 'currentTab', 'search'));
    }

    public function show(Payment $payment)
    {
        return view('admin.payments.show', compact('payment'));
    }

    public function validatePayment(Payment $payment)
    {
        // 1. Update payment status
        $payment->update([
            'status' => 'completed',
            'paid_at' => now()
        ]);

        // 2. Use unified finalization logic from PaymentService
        $this->paymentService->finalizePurchase($payment);

        return back()->with('success', __('Payment validated, access granted, and revenue recorded successfully.'));
    }

    public function refund(Payment $payment)
    {
        $payment->update(['status' => 'refunded']);

        return back()->with('success', __('Payment refunded successfully.'));
    }

    public function monthlyReport()
    {
        $payments = Payment::where('status', 'completed')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->get();
        $totalMonthlyRevenue = $payments->sum('amount');

        return view('admin.payments.monthly-report', compact('payments', 'totalMonthlyRevenue'));
    }

    public function annualReport()
    {
        $payments = Payment::where('status', 'completed')
            ->whereYear('paid_at', now()->year)
            ->get();
        $totalAnnualRevenue = $payments->sum('amount');

        return view('admin.payments.annual-report', compact('payments', 'totalAnnualRevenue'));
    }
}
