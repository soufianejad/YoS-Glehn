@extends('layouts.author')

@section('content')
<div class="container-fluid">
    <a href="{{ route('author.reviews') }}" class="btn btn-secondary mb-4">
        <i class="fas fa-arrow-left"></i> {{ __('Back to All Reviews') }}
    </a>

    <h1 class="mb-4 section-title">{{ __('Review Details') }}</h1>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>{{ __('Review for:') }} <strong>{{ $review->book->title }}</strong></span>
            <small>{{ $review->created_at->format('M d, Y, h:i A') }}</small>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <h5 class="card-title">{{ __('Reviewed by:') }} {{ $review->user->name }}</h5>
                <p class="card-text">
                    <strong>{{ __('Rating:') }}</strong>
                    @for ($i = 0; $i < 5; $i++)
                        <i class="bi bi-star{{ $i < $review->rating ? '-fill' : '' }}" style="color: #ffc107;"></i>
                    @endfor
                </p>
            </div>
            <hr>
            <p class="card-text mt-3">
                {{ $review->review_text }}
            </p>
        </div>
        <div class="card-footer text-muted">
            {{ __('Book:') }} <a href="{{ route('author.books.show', $review->book->id) }}">{{ $review->book->title }}</a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
@endpush
