<!-- resources/views/admin/payments/annual-report.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Annual Payment Report') }} ({{ now()->format('Y') }})</h1>

    <div class="card mb-4">
        <div class="card-header">{{ __('Summary') }}</div>
        <div class="card-body">
            <p><strong>{{ __('Total Annual Revenue:') }}</strong> ${{ number_format($totalAnnualRevenue, 2) }}</p>
            <p><strong>{{ __('Number of Payments:') }}</strong> {{ $payments->count() }}</p>
        </div>
    </div>

    <h2>{{ __('All Payments this Year') }}</h2>
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('User') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Paid At') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->user->name ?? __('N/A') }}</td>
                    <td>{{ $payment->payment_type }}</td>
                    <td>${{ $payment->amount }}</td>
                    <td>{{ $payment->status }}</td>
                    <td>{{ $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : __('N/A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">{{ __('No payments recorded this year.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
