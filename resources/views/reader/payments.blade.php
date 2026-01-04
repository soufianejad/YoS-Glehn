@extends('layouts.dashboard')

@section('title', __('My Payments'))
@section('header', __('My Payments'))

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">{{ __('Payment History') }}</h4>
        </div>
        <div class="card-body">
            @if ($payments->isEmpty())
                <div class="text-center text-muted">
                    <p>{{ __('You have not made any payments yet.') }}</p>
                    <a href="{{ route('library.index') }}" class="btn btn-primary">{{ __('Browse Books') }}</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">{{ __('Transaction ID') }}</th>
                                <th scope="col">{{ __('Type') }}</th>
                                <th scope="col">{{ __('Amount') }}</th>
                                <th scope="col">{{ __('Provider') }}</th>
                                <th scope="col">{{ __('Status') }}</th>
                                <th scope="col">{{ __('Date') }}</th>
                                <th scope="col">{{ __('Item') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td class="font-monospace small">{{ $payment->transaction_id }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</td>
                                    <td>{{ $payment->amount }} {{ strtoupper($payment->currency) }}</td>
                                    <td>{{ ucfirst($payment->payment_provider) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $payment->status == 'completed' ? 'success' : ($payment->status == 'failed' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($payment->paid_at)
                                            {{ $payment->paid_at->format('d M Y, H:i') }}
                                        @else
                                            {{ $payment->created_at->format('d M Y, H:i') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($payment->book)
                                            <a href="{{ route('book.show', $payment->book->slug) }}" class="btn btn-sm btn-outline-secondary">{{ __('View Book') }}</a>
                                        @elseif ($payment->subscription)
                                            <a href="{{ route('reader.subscription') }}" class="btn btn-sm btn-outline-secondary">{{ __('View Subscription') }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
