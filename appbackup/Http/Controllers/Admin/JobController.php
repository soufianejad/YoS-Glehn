<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RevenueCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JobController extends Controller
{
    protected $revenueCalculator;

    public function __construct(RevenueCalculatorService $revenueCalculator)
    {
        $this->revenueCalculator = $revenueCalculator;
    }

    public function distributeSubscriptionRevenues(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $date = Carbon::createFromFormat('Y-m', $month);

        $result = $this->revenueCalculator->distributeSubscriptionRevenues($date);

        if ($result['success']) {
            return back()->with('success', 'Subscription revenues for '.$month.' have been distributed successfully. '.$result['revenues_distributed'].' revenues distributed for a total of '.$result['total_amount'].'.');
        } else {
            return back()->with('error', 'An error occurred while distributing subscription revenues: '.$result['error']);
        }
    }
}
