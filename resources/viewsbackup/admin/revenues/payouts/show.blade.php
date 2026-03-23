<!-- resources/views/admin/revenues/payouts/show.blade.php -->

@extends('@extends('layouts.dashboard')outs.dashboard')')

@section('content')
<div class="container">
    <h1>Payout Details: {{ $payout->payout_reference }}</h1>

    <div class="card">
        <div class="card-header">{{ __('Payout Information') }}</div>
        <div class="card-body">
            <p><strong>ID:</strong> {{ $payout->id }}</p>
            <p><strong>{{ __('Reference:') }}</strong> {{ $payout->payout_reference }}</p>
            <p><strong>{{ __('Author:') }}</strong> {{ $payout->author->name ?? 'N/A' }} ({{ $payout->author->email ?? 'N/A' }})</p>
            <p><strong>{{ __('Amount:') }}</strong> ${{ $payout->amount }} {{ $payout->currency }}</p>
            <p><strong>{{ __('Payment Method:') }}</strong> {{ $payout->payment_method }}</p>
            <p><strong>{{ __('Payment Details:') }}</strong> {{ $payout->payment_details }}</p>
            <p><strong>{{ __('Period Start:') }}</strong> 
                {{ $payout->period_start ? \Carbon\Carbon::parse($payout->period_start)->format('M d, Y') : 'N/A' }}
            </p>
            <p><strong>{{ __('Period End:') }}</strong> 
                {{ $payout->period_end ? \Carbon\Carbon::parse($payout->period_end)->format('M d, Y') : 'N/A' }}
            </p>
            <p><strong>{{ __('Status:') }}</strong> {{ $payout->status }}</p>
            <p><strong>{{ __('Processed At:') }}</strong> 
                {{ $payout->processed_at ? \Carbon\Carbon::parse($payout->processed_at)->format('M d, Y H:i') : 'N/A' }}
            </p>
            <p><strong>{{ __('Created At:') }}</strong> 
                {{ $payout->created_at ? \Carbon\Carbon::parse($payout->created_at)->format('M d, Y H:i') : 'N/A' }}
            </p>
            <p><strong>{{ __('Last Updated:') }}</strong> 
                {{ $payout->updated_at ? \Carbon\Carbon::parse($payout->updated_at)->format('M d, Y H:i') : 'N/A' }}
            </p>
            
        </div>
    </div>

    <div class="mt-3">
        @if($payout->isPending())
            <form action="{{ route('admin.revenues.payouts.confirm', $payout) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">{{ __('Confirm Payout') }}</button>
            </form>
            <form action="{{ route('admin.revenues.payouts.cancel', $payout) }}" method="POST" class="d-inline ms-2">
                @csrf
                <button type="submit" class="btn btn-danger">{{ __('Cancel Payout') }}</button>
            </form>
        @endif
    </div>

    <a href="{{ route('admin.revenues.payouts.index') }}" class="btn btn-secondary mt-3">{{ __('Back to Payouts') }}</a>
</div>
@endsection
