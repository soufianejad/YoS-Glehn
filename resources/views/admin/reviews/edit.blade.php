@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
    <h1>{{ __('Edit Review') }}</h1>

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
            <form action="{{ route('admin.reviews.update', $review) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="rating" class="form-label">{{ __('Rating (1-5)') }}</label>
                    <input type="number" name="rating" id="rating" class="form-control @error('rating') is-invalid @enderror" value="{{ old('rating', $review->rating) }}" min="1" max="5" required>
                    @error('rating')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="comment" class="form-label">{{ __('Comment') }}</label>
                    <textarea name="comment" id="comment" rows="5" class="form-control @error('comment') is-invalid @enderror" required>{{ old('comment', $review->comment) }}</textarea>
                    @error('comment')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="is_approved" class="form-label">{{ __('Approved Status') }}</label>
                    <select name="is_approved" id="is_approved" class="form-select @error('is_approved') is-invalid @enderror" required>
                        <option value="1" {{ old('is_approved', $review->is_approved) ? 'selected' : '' }}>{{ __('Approved') }}</option>
                        <option value="0" {{ !old('is_approved', $review->is_approved) ? 'selected' : '' }}>{{ __('Pending / Rejected') }}</option>
                    </select>
                    @error('is_approved')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ __('Update Review') }}</button>
                    <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
