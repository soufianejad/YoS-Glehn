<!-- resources/views/author/dashboard/profile.blade.php -->

@extends('layouts.author')

@section('content')
<div class="container">
    <h1>{{ __('Author Profile') }}</h1>

    <div class="card">
        <div class="card-header">{{ __('Profile Information') }}</div>
        <div class="card-body">
            <p><strong>{{ __('Name:') }}</strong> {{ $author->name }}</p>
            <p><strong>{{ __('Email:') }}</strong> {{ $author->email }}</p>
            <p><strong>{{ __('Registered At:') }}</strong> {{ $author->created_at->format('M d, Y H:i') }}</p>
            <p><strong>{{ __('Last Updated:') }}</strong> {{ $author->updated_at->format('M d, Y H:i') }}</p>
        </div>
    </div>

    <form action="{{ route('author.profile.update') }}" method="POST" class="mt-3">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $author->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email address') }}</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $author->email) }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Update Profile') }}</button>
    </form>
</div>
@endsection
