<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Subscription;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function getMethodsByCountry(Request $request)
    {
        $country = $request->query('country', 'CI');
        $data = $this->paymentService->getAvailablePaymentMethods(null, $country);
        
        return response()->json($data);
    }

    public function callback(Request $request, $service)
    {
        Log::info("Payment Callback Received for {$service}:", $request->all());

        $reference = null;
        $status = 'failed';
        $providerRef = null;

        if ($service === 'touchpay') {
            $reference = $request->input('partner_transaction_id');
            $status = (strtoupper($request->input('status')) === 'SUCCESSFUL') ? 'completed' : 'failed';
            $providerRef = $request->input('gu_transaction_id');
        } elseif ($service === 'paiementpro') {
            $reference = $request->input('referenceNumber');
            $status = ($request->input('responsecode') == '0') ? 'completed' : 'failed';
            $providerRef = $request->input('payId');
        }

        if ($reference) {
            $payment = Payment::where('transaction_id', $reference)->first();
            if ($payment && $payment->status === 'pending') {
                $payment->update([
                    'status' => $status,
                    'paid_at' => $status === 'completed' ? now() : null,
                    'payment_details' => array_merge($payment->payment_details ?? [], ['provider_ref' => $providerRef])
                ]);

                if ($status === 'completed') {
                    $this->paymentService->finalizePurchase($payment);
                }
            }
        }

        return response('OK', 200);
    }

    // Removed local finalizePurchase method in favor of PaymentService

    public function success()
    {
        $url = Session::get('payment_return_url', route('home'));
        Session::forget('payment_return_url');
        return view('payment.success', compact('url'));
    }

    public function failed()
    {
        return view('payment.failed');
    }

    public function pending()
    {
        return view('payment.pending');
    }
}
