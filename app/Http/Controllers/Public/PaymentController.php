<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PaymentController extends Controller
{
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
                    $this->finalizePurchase($payment);
                }
            }
        }

        return response('OK', 200);
    }

    private function finalizePurchase(Payment $payment)
    {
        if ($payment->payment_type === 'book_purchase') {
            Purchase::create([
                'user_id' => $payment->user_id,
                'book_id' => $payment->book_id,
                'payment_id' => $payment->id,
                'purchase_type' => $payment->payment_details['purchase_type'] ?? 'pdf',
                'price' => $payment->amount,
                'is_active' => true,
            ]);
        } elseif ($payment->payment_type === 'subscription') {
            $subscription = Subscription::find($payment->subscription_id);
            if ($subscription) {
                $subscription->update([
                    'status' => 'active',
                    'start_date' => now(),
                    'end_date' => now()->addMonths($subscription->plan->duration_months ?? 1),
                ]);
            }
        }
    }

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
