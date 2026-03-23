<!-- resources/views/admin/dashboard/activity-report.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Admin Activity Report') }}</h1>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">{{ __('Recent Users') }}</div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($recentUsers as $user)
                            <li class="list-group-item">{{ $user->name }} ({{ $user->email }}) - {{ $user->created_at->diffForHumans() }}</li>
                        @empty
                            <li class="list-group-item">{{ __('No recent users.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">{{ __('Recent Books') }}</div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($recentBooks as $book)
                            <li class="list-group-item">{{ $book->title }} {{ __('by') }} {{ $book->author->name }} - {{ $book->created_at->diffForHumans() }}</li>
                        @empty
                            <li class="list-group-item">{{ __('No recent books.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">{{ __('Recent Reviews') }}</div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($recentReviews as $review)
                            <li class="list-group-item">{{ $review->user->name }} {{ __('reviewed') }} "{{ $review->book->title }}" ({{ $review->rating }}/5) - {{ $review->created_at->diffForHumans() }}</li>
                        @empty
                            <li class="list-group-item">{{ __('No recent reviews.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
