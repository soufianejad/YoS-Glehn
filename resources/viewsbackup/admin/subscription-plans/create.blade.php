<!-- resources/views/admin/subscription-plans/create.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Create New Subscription Plan') }}</h1>

    <form action="{{ route('admin.subscription-plans.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Plan Name') }}</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">{{ __('Description') }}</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">{{ __('Plan Type') }}</label>
            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                <option value="individual" {{ old('type') == 'individual' ? 'selected' : '' }}>{{ __('Individual') }}</option>
                <option value="school" {{ old('type') == 'school' ? 'selected' : '' }}>{{ __('School') }}</option>
            </select>
            @error('type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">{{ __('Price') }}</label>
            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required>
            @error('price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="duration_days" class="form-label">Duration (Days)</label>
            <input type="number" class="form-control @error('duration_days') is-invalid @enderror" id="duration_days" name="duration_days" value="{{ old('duration_days') }}" required>
            @error('duration_days')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="max_students" class="form-label">Max Students (for School plans)</label>
            <input type="number" class="form-control @error('max_students') is-invalid @enderror" id="max_students" name="max_students" value="{{ old('max_students') }}">
            @error('max_students')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="pdf_access" name="pdf_access" value="1" {{ old('pdf_access') ? 'checked' : '' }}>
            <label class="form-check-label" for="pdf_access">{{ __('PDF Access') }}</label>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="audio_access" name="audio_access" value="1" {{ old('audio_access') ? 'checked' : '' }}>
            <label class="form-check-label" for="audio_access">{{ __('Audio Access') }}</label>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="download_access" name="download_access" value="1" {{ old('download_access') ? 'checked' : '' }}>
            <label class="form-check-label" for="download_access">{{ __('Download Access') }}</label>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="quiz_access" name="quiz_access" value="1" {{ old('quiz_access') ? 'checked' : '' }}>
            <label class="form-check-label" for="quiz_access">{{ __('Quiz Access') }}</label>
        </div>

        <div class="mb-3">
            <label for="order" class="form-label">{{ __('Order') }}</label>
            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', 0) }}">
            @error('order')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">{{ __('Is Active') }}</label>
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Create Plan') }}</button>
    </form>
</div>
@endsection
