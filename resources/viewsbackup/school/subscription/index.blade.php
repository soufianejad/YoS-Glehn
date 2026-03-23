<!-- resources/views/school/subscription/index.blade.php -->

@extends('layouts.school')

@section('content')
<div class="container">
    <h1>{{ __('School Subscription - ') }} {{ $school->name }}</h1>

    <div class="card mb-4">
        <div class="card-header">
            {{ __('Current Subscription Plan') }}
        </div>
        <div class="card-body">
            @if ($subscription && $subscription->subscriptionPlan)
                <p><strong>{{ __('Plan Name:') }}</strong> {{ $subscription->subscriptionPlan->name }}</p>
                <p><strong>{{ __('Status:') }}</strong>
                    @if ($subscription->isActive())
                        <span class="badge bg-success">{{ __('Active') }}</span>
                    @else
                        <span class="badge bg-danger">{{ __('Inactive') }}</span>
                    @endif
                </p>
                <p><strong>{{ __('Student Limit:') }}</strong> {{ $subscription->max_students ?? __('Unlimited') }}</p>
                <p><strong>{{ __('Current Students:') }}</strong> {{ $school->current_students }}</p>
                <p><strong>{{ __('Starts At:') }}</strong> {{ $subscription->start_date->format('M d, Y') }}</p>
                <p><strong>{{ __('Ends At:') }}</strong> {{ $subscription->end_date->format('M d, Y') }}</p>
                <p><strong>{{ __('Price:') }}</strong> {{ $subscription->price }}</p>
                {{-- Add buttons for upgrade/downgrade/renew here --}}
                <!-- <button class="btn btn-primary me-2">{{ __('Upgrade/Downgrade Plan') }}</button> -->
                <!-- <button class="btn btn-success">{{ __('Renew Subscription') }}</button> -->
                <a href="{{ route('school.subscription.plans') }}" class="btn btn-info">{{ __('Changer de Plan') }}</a>
            @else
                <p>{{ __('No active subscription found. Please subscribe to a plan.') }}</p>
                <a href="{{ route('school.subscription.plans') }}" class="btn btn-primary">{{ __('View Plans') }}</a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            {{ __('Payment History') }}
        </div>
        <div class="card-body">
            @if ($payments->count() > 0)
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Method') }}</th>
                            <th>{{ __('Transaction ID') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                <td>{{ $payment->amount }} {{ $payment->currency }}</td>
                                <td><span class="badge bg-{{ $payment->status == 'completed' ? 'success' : 'warning' }}">{{ $payment->status }}</span></td>
                                <td>{{ $payment->payment_method }}</td>
                                <td>{{ $payment->transaction_id }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $payments->links() }}
            @else
                <p>{{ __('No payment history found.') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
