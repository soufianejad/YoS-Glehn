@extends('layouts.school')

@section('content')
<div class="container">
    <h1>{{ __('Add New Parent') }}</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('school.parents.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="first_name" class="form-label">{{ __('First Name') }}</label>
                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">{{ __('Last Name') }}</label>
                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('Password') }}</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>

                <div class="mb-3">
                    <label for="student_ids" class="form-label">{{ __('Link Students') }}</label>
                    <select class="form-control @error('student_ids') is-invalid @enderror" id="student_ids" name="student_ids[]" multiple>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }}</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">{{ __('Select one or more students to link to this parent. Only students without a parent are shown.') }}</small>
                    @error('student_ids')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">{{ __('Add Parent') }}</button>
                <a href="{{ route('school.parents.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection