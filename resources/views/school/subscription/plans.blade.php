@extends('layouts.school')

@section('content')
<div class="container">
    <h1>{{ __('Choose a Subscription Plan') }}</h1>
    <p>{{ __('Select the plan that best fits your school\'s needs.') }}</p>

    <div class="row">
        @forelse ($plans as $plan)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="my-0 fw-normal">{{ $plan->name }}</h4>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h1 class="card-title pricing-card-title">$
{{ number_format($plan->price, 2) }} <small class="text-muted fw-light">/ an</small></h1>
                        <p>{{ $plan->description }}</p>
                        <ul class="list-unstyled mt-3 mb-4">
                            <li><i class="fas fa-users me-2"></i> {{ __('Up to') }} {{ $plan->max_students }} {{ __('students') }}</li>
                            <li><i class="fas fa-book me-2"></i> {{ $plan->pdf_access ? __('PDF Access') : '' }}</li>
                            <li><i class="fas fa-headphones me-2"></i> {{ $plan->audio_access ? __('Audio Access') : '' }}</li>
                            <li><i class="fas fa-download me-2"></i> {{ $plan->download_access ? __('Download Access') : '' }}</li>
                            <li><i class="fas fa-question-circle me-2"></i> {{ $plan->quiz_access ? __('Quiz Access') : '' }}</li>
                        </ul>
                        <form action="{{ route('school.subscription.subscribe', $plan) }}" method="POST" class="mt-auto">
                            @csrf
                            <button type="submit" class="w-100 btn btn-lg btn-primary">{{ __('Choose Plan') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col">
                <div class="alert alert-warning" role="alert">
                    {{ __('No subscription plans are available at the moment. Please check back later.') }}
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
