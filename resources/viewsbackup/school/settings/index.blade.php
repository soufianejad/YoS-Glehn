<!-- resources/views/school/settings/index.blade.php -->

@extends('layouts.school')

@section('content')
<div class="container">
    <h1>{{ __('School Settings - ') }} {{ $school->name }}</h1>

    {{-- Main Settings Form --}}
    <form action="{{ route('school.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('School Name') }}</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $school->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('School Email') }}</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $school->email) }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="access_code" class="form-label">{{ __('Student Access Code') }}</label>
            <div class="input-group">
                <input type="text" class="form-control" id="access_code" value="{{ $school->access_code }}" readonly>
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="{{ __('regenerateAccessCodeModal') }}">{{ __('Regenerate') }}</button>
            </div>
            <small class="form-text text-muted">{{ __('Students can use this code to register and join your school.') }}</small>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">{{ __('Address') }}</label>
            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $school->address) }}">
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="city" class="form-label">{{ __('City') }}</label>
            <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $school->city) }}">
            @error('city')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="country" class="form-label">{{ __('Country') }}</label>
            <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country" value="{{ old('country', $school->country) }}">
            @error('country')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">{{ __('Phone') }}</label>
            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $school->phone) }}">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="logo" class="form-label">{{ __('Logo') }}</label>
            @if($school->logo)
                <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ __('Current Logo') }}" class="img-thumbnail mb-2" width="150">
            @endif
            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo">
            @error('logo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="banner_image" class="form-label">{{ __('Dashboard Banner Image') }}</label>
            @if($school->banner_image)
                <img src="{{ asset('storage/' . $school->banner_image) }}" alt="{{ __('Current Banner Image') }}" class="img-thumbnail mb-2" width="300">
            @endif
            <input type="file" class="form-control @error('banner_image') is-invalid @enderror" id="banner_image" name="banner_image">
            @error('banner_image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="primary_color" class="form-label">{{ __('Primary Color') }}</label>
            <input type="color" class="form-control form-control-color @error('primary_color') is-invalid @enderror" id="primary_color" name="primary_color" value="{{ old('primary_color', $school->primary_color) }}">
            @error('primary_color')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-check form-switch">
            <input type="hidden" name="students_can_view_classmates" value="0">
            <input class="form-check-input" type="checkbox" id="students_can_view_classmates" name="students_can_view_classmates" value="1" {{ old('students_can_view_classmates', $school->students_can_view_classmates) ? 'checked' : '' }}>
            <label class="form-check-label" for="students_can_view_classmates">{{ __('Allow students to view their classmates') }}</label>
            <small class="form-text text-muted d-block">{{ __('If disabled, students will not be able to view the list of other students in their classes.') }}</small>
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Update Settings') }}</button>
    </form>
    {{-- End of Main Settings Form --}}

</div>

<!-- Regenerate Access Code Modal (Now outside the main form) -->
<div class="modal fade" id="regenerateAccessCodeModal" tabindex="-1" aria-labelledby="regenerateAccessCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="regenerateAccessCodeModalLabel">{{ __('Confirm Regeneration') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body">
                {{ __('Are you sure you want to regenerate the access code? The old code will no longer be valid.') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('regenerate-code-form').submit();">{{ __('Regenerate Code') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for regenerating code -->
<form id="regenerate-code-form" action="{{ route('school.settings.regenerate-access-code') }}" method="POST" style="display: none;">
    @csrf
</form>

@endsection