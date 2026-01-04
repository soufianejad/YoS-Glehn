<!-- resources/views/admin/categories/create.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Create New Category') }}</h1>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Category Name') }}</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">{{ __('Description') }}</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="space" class="form-label">{{ __('Space') }}</label>
            <select class="form-control @error('space') is-invalid @enderror" id="space" name="space" required>
                <option value="public" {{ old('space') == 'public' ? 'selected' : '' }}>{{ __('Public') }}</option>
                <option value="educational" {{ old('space') == 'educational' ? 'selected' : '' }}>{{ __('Educational') }}</option>
                <option value="adult" {{ old('space') == 'adult' ? 'selected' : '' }}>{{ __('Adult') }}</option>
            </select>
            @error('space')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="icon" class="form-label">{{ __('Icon (e.g., Font Awesome class)') }}</label>
            <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon') }}">
            @error('icon')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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

        <button type="submit" class="btn btn-primary">{{ __('Create Category') }}</button>
    </form>
</div>
@endsection
