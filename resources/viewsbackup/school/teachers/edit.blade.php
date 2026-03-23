@extends('layouts.school')

@section('content')
<div class="container">
    <h1>{{ __('Edit Teacher') }}: {{ $teacher->first_name }} {{ $teacher->last_name }}</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('school.teachers.update', $teacher) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="first_name" class="form-label">{{ __('First Name') }}</label>
                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $teacher->first_name) }}" required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">{{ __('Last Name') }}</label>
                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $teacher->last_name) }}" required>
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $teacher->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('New Password') }}</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                    <small class="form-text text-muted">{{ __('Leave blank to keep the current password.') }}</small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Update Teacher') }}</button>
                <a href="{{ route('school.teachers.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection
