<!-- resources/views/admin/payments/show.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Payment Details: ') . $payment->transaction_id }}</h1>

    <div class="card">
        <div class="card-header">{{ __('Payment Information') }}</div>
        <div class="card-body">
            <p><strong>{{ __('ID:') }}</strong> {{ $payment->id }}</p>
            <p><strong>{{ __('Transaction ID:') }}</strong> {{ $payment->transaction_id }}</p>
            <p><strong>{{ __('User:') }}</strong> {{ $payment->user->name ?? __('N/A') }} ({{ $payment->user->email ?? __('N/A') }})</p>
            <p><strong>{{ __('Payment Type:') }}</strong> {{ $payment->payment_type }}</p>
            <p><strong>{{ __('Book:') }}</strong> {{ $payment->book->title ?? __('N/A') }}</p>
            <p><strong>{{ __('Subscription Plan:') }}</strong> {{ $payment->subscription->subscriptionPlan->name ?? __('N/A') }}</p>
            <p><strong>{{ __('Amount:') }}</strong> ${{ $payment->amount }} {{ $payment->currency }}</p>
            <p><strong>{{ __('Payment Method:') }}</strong> {{ $payment->payment_method }}</p>
            <p><strong>{{ __('Payment Provider:') }}</strong> {{ $payment->payment_provider }}</p>
            <p><strong>{{ __('Status:') }}</strong> {{ $payment->status }}</p>
            <p><strong>{{ __('Paid At:') }}</strong> {{ $payment->paid_at ? $payment->paid_at->format('M d, Y H:i') : __('N/A') }}</p>
            <p><strong>{{ __('Payment Details:') }}</strong> {{ json_encode($payment->payment_details) }}</p>
            <p><strong>{{ __('Created At:') }}</strong> {{ $payment->created_at->format('M d, Y H:i') }}</p>
            <p><strong>{{ __('Last Updated:') }}</strong> {{ $payment->updated_at->format('M d, Y H:i') }}</p>
        </div>
    </div>

    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary mt-3">{{ __('Back to Payments') }}</a>
</div>
@endsection
