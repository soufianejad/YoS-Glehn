<!-- resources/views/subscription/index.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('My Subscription') }}</h1>

    @if($subscription)
        <div class="card">
            <div class="card-header">{{ __('Current Subscription') }}</div>
            <div class="card-body">
                <p><strong>{{ __('Plan:') }}</strong> {{ $subscription->subscriptionPlan->name }}</p>
                <p><strong>{{ __('Status:') }}</strong> {{ $subscription->status }}</p>
                <p><strong>{{ __('Starts:') }}</strong> {{ $subscription->start_date->format('M d, Y') }}</p>
                <p><strong>{{ __('Ends:') }}</strong> {{ $subscription->end_date->format('M d, Y') }}</p>
                <p><strong>{{ __('Auto Renew:') }}</strong> {{ $subscription->auto_renew ? 'Yes' : 'No' }}</p>
                <p><strong>{{ __('Days Remaining:') }}</strong> {{ $subscription->daysRemaining() }}</p>

                <form action="{{ route('subscription.cancel') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">{{ __('Cancel Subscription') }}</button>
                </form>
                <form action="{{ route('subscription.renew') }}" method="POST" class="d-inline ms-2">
                    @csrf
                    <button type="submit" class="btn btn-success">{{ __('Renew Subscription') }}</button>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            You do not have an active subscription. <a href="{{ route('subscription.plans') }}">{{ __('View Plans') }}</a>
        </div>
    @endif
</div>
@endsection
