@extends('layouts.school')

@section('content')
<div class="container">
    <h1>{{ __('Edit Parent') }}: {{ $parent->first_name }} {{ $parent->last_name }}</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('school.parents.update', $parent) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="first_name" class="form-label">{{ __('First Name') }}</label>
                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $parent->first_name) }}" required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">{{ __('Last Name') }}</label>
                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $parent->last_name) }}" required>
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $parent->email) }}" required>
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

                 <div class="mb-3">
                    <label for="student_ids" class="form-label">{{ __('Link Students') }}</label>
                    <select class="form-control @error('student_ids') is-invalid @enderror" id="student_ids" name="student_ids[]" multiple>
                        @php
                            $linkedStudentIds = $parent->children->pluck('id')->toArray();
                        @endphp
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" {{ in_array($student->id, $linkedStudentIds) ? 'selected' : '' }}>
                                {{ $student->first_name }} {{ $student->last_name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">{{ __('Select students to link to this parent. Only students without a parent or already linked to this parent are shown.') }}</small>
                    @error('student_ids')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">{{ __('Update Parent') }}</button>
                <a href="{{ route('school.parents.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection
