@extends('layouts.dashboard')

@section('title', __('My Subscription'))

@section('content')
<div class="container py-4">
<h1 class="mb-4">{{ __('My Subscription') }}</h1>
    @if (session('success'))
        <div class="alert alert-success text-center" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger text-center" role="alert">
            {{ session('error') }}
        </div>
    @endif

    {{-- Current Subscription Section --}}
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-primary text-white">
            <h2 class="h5 mb-0">{{ __('Current Plan') }}</h2>
        </div>
        <div class="card-body">
            @if($subscription)
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="card-title text-primary">{{ $subscription->subscriptionPlan->name }}</h3>
                        <p class="card-text mb-1"><strong>{{ __('Status:') }}</strong>
                            @if($subscription->status === 'active')
                                <span class="badge bg-success">{{ __('Active') }}</span>
                            @elseif($subscription->status === 'cancelled')
                                <span class="badge bg-warning text-dark">{{ __('Cancelled') }}</span>
                            @else
                                <span class="badge bg-danger">{{ ucfirst($subscription->status) }}</span>
                            @endif
                        </p>
                        <p class="card-text mb-1"><strong>{{ __('Price:') }}</strong> {{ number_format($subscription->subscriptionPlan->price, 0) }} XOF / {{ $subscription->subscriptionPlan->duration_days }} {{ __('days') }}</p>
                        <p class="card-text mb-1"><strong>{{ __('Starts On:') }}</strong> {{ $subscription->start_date->format('Y-m-d') }}</p>
                        <p class="card-text mb-1"><strong>{{ __('Ends On:') }}</strong> {{ $subscription->end_date->format('Y-m-d') }}</p>
                        @if($subscription->auto_renew)
                            <p class="card-text text-muted"><em>{{ __('Auto-renewal is enabled.') }}</em></p>
                        @else
                            <p class="card-text text-muted"><em>{{ __('Auto-renewal is disabled.') }}</em></p>
                        @endif
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        @if($subscription->status === 'active')
                            <form action="{{ route('reader.subscription.renew') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning me-2 mb-2 mb-md-0">{{ __('Renew Now') }}</button>
                            </form>
                            <form action="{{ route('reader.subscription.cancel', $subscription) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('Are you sure you want to cancel your subscription?') }}')">{{ __('Cancel Subscription') }}</button>
                            </form>
                        @endif
                        <a href="{{ route('reader.payments') }}" class="btn btn-outline-secondary mt-3 w-100">{{ __('View Payment History') }}</a>
                    </div>
                </div>
            @else
                <div class="alert alert-info text-center mb-0" role="alert">
                    <h4 class="alert-heading">{{ __('No Active Subscription') }}</h4>
                    <p>{{ __('It looks like you do not have an active subscription. Subscribe today to get unlimited access to our library!') }}</p>
                    <hr>
                    <a href="{{ route('subscription.plans') }}" class="btn btn-primary">{{ __('Browse Plans') }}</a>
                </div>
            @endif
        </div>
    </div>

    {{-- Available Plans Section --}}
    <div class="mb-5">
        <h2 class="h4 mb-4">{{ __('Available Subscription Plans') }}</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach($subscriptionPlans as $plan)
                <div class="col">
                    <div class="card h-100 shadow-sm border-{{ $subscription && $subscription->subscription_plan_id === $plan->id ? 'primary' : 'light' }}">
                        <div class="card-header text-center bg-light">
                            <h3 class="h5 my-2">{{ $plan->name }}</h3>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="text-center mb-3">
                                <span class="display-6 fw-bold text-primary">{{ number_format($plan->price, 0) }}</span>
                                <span class="text-muted">XOF / {{ $plan->duration_days }} {{ __('days') }}</span>
                            </div>
                            <p class="text-center text-muted">{{ $plan->description }}</p>
                            <ul class="list-unstyled flex-grow-1">
                                {{-- Dynamically list features if available --}}
                                @if($plan->features)
                                    @foreach(json_decode($plan->features) as $feature)
                                        <li><i class="fas fa-check-circle text-success me-2"></i>{{ $feature }}</li>
                                    @endforeach
                                @else
                                    <li><i class="fas fa-check-circle text-success me-2"></i>{{ __('Unlimited access to books') }}</li>
                                @endif
                            </ul>
                            <div class="mt-auto">
                                @if($subscription && $subscription->subscription_plan_id === $plan->id)
                                    <button class="btn btn-primary w-100" disabled>{{ __('Current Plan') }}</button>
                                @else
                                    <a href="{{ route('subscription.plans') }}" class="btn btn-outline-primary w-100">{{ __('Choose Plan') }}</a>
                                    {{-- The 'subscribe' route handles the actual purchase/upgrade logic --}}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Subscription History Section --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h2 class="h5 mb-0">{{ __('Subscription History') }}</h2>
        </div>
        <div class="card-body">
            @if($subscriptionHistory->count())
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Plan') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Start Date') }}</th>
                                <th>{{ __('End Date') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptionHistory as $hist)
                                <tr>
                                    <td>{{ $hist->subscriptionPlan->name }}</td>
                                    <td>{{ number_format($hist->subscriptionPlan->price, 0) }} XOF</td>
                                    <td>{{ $hist->start_date->format('Y-m-d') }}</td>
                                    <td>{{ $hist->end_date->format('Y-m-d') }}</td>
                                    <td>
                                        @if($hist->status === 'active')
                                            <span class="badge bg-success">{{ __('Active') }}</span>
                                        @elseif($hist->status === 'cancelled')
                                            <span class="badge bg-warning text-dark">{{ __('Cancelled') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ ucfirst($hist->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-muted mb-0">{{ __('No past subscriptions found.') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
