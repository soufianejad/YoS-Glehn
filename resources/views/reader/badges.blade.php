@extends('layouts.dashboard')

@section('title', 'My Badges')
@section('header', 'My Badges & Achievements')

@section('content')
<div class="container py-4">
    <div class="mb-5">
        <h2 class="h4">{{ __('Badges Earned') }}</h2>
        <p class="text-muted">{{ __('Congratulations on these achievements!') }}</p>
        <div class="row">
            @php
                $earnedBadges = $allBadges->whereIn('id', $earnedBadgeIds);
            @endphp
            @forelse ($earnedBadges as $badge)
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card h-100 text-center shadow-sm">
                        <div class="card-body">
                            <i style="font-size: 45px;" class="{{ $badge->icon }}"></i>
                            <h5 class="card-title">{{ $badge->name }}</h5>
                            <p class="card-text small">{{ $badge->description }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col">
                    <p>{{ __("You haven't earned any badges yet. Keep reading and completing quizzes to unlock them!") }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <hr>

    <div class="mt-5">
        <h2 class="h4">{{ __('Badges to Unlock') }}</h2>
        <p class="text-muted">{{ __('Here is what you can aim for next.') }}</p>
        <div class="row">
            @php
                $unearnedBadges = $allBadges->whereNotIn('id', $earnedBadgeIds);
            @endphp
            @forelse ($unearnedBadges as $badge)
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card h-100 text-center bg-light" style="opacity: 0.7;">
                        <div class="card-body">
                            <i style="font-size: 45px;" class="{{ $badge->icon }}"></i>
                            <h5 class="card-title">{{ $badge->name }}</h5>
                            <p class="card-text small">{{ $badge->description }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col">
                    <p>{{ __('You have earned all available badges. Congratulations!') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
