@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <h1>{{ __('Review Details') }}</h1>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Review from') }} {{ $review->user->name }} {{ __('for') }} {{ $review->book->title }}</h3>
            <div class="card-tools">
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> {{ __('Back to Reviews') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <p><strong>{{ __('User:') }}</strong> {{ $review->user->name }} ({{ $review->user->email }})</p>
            <p><strong>{{ __('Book:') }}</strong> <a href="{{ route('book.show', $review->book->slug) }}" target="_blank">{{ $review->book->title }}</a></p>
            <p><strong>{{ __('Rating:') }}</strong> {{ $review->rating }}/5</p>
            <p><strong>{{ __('Comment:') }}</strong> {{ $review->comment }}</p>
            <p><strong>{{ __('Submitted On:') }}</strong> {{ $review->created_at->format('M d, Y H:i') }}</p>
            <p><strong>{{ __('Approved:') }}</strong>
                @if($review->is_approved)
                    <span class="badge bg-success">{{ __('Yes') }}</span>
                @else
                    <span class="badge bg-danger">{{ __('No') }}</span>
                @endif
            </p>

            <hr>

            <div class="d-flex justify-content-start gap-2">
                @if(!$review->is_approved)
                    <form action="{{ route('admin.reviews.approve', $review) }}" method="POST" style="display:inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> {{ __('Approve') }}</button>
                    </form>
                    <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" style="display:inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-warning"><i class="fas fa-times"></i> {{ __('Reject') }}</button>
                    </form>
                @else
                    <form action="{{ route('admin.reviews.reject', $review) }}" method="POST" style="display:inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-warning"><i class="fas fa-times"></i> {{ __('Unapprove') }}</button>
                    </form>
                @endif
                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this review?') }}');" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> {{ __('Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection