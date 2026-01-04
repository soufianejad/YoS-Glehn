<!-- resources/views/admin/settings/general.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('General Settings') }}</h1>

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="site_name" class="form-label">{{ __('Site Name') }}</label>
            <input type="text" class="form-control @error('site_name') is-invalid @enderror" id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" required>
            @error('site_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="site_description" class="form-label">{{ __('Site Description') }}</label>
            <textarea class="form-control @error('site_description') is-invalid @enderror" id="site_description" name="site_description" rows="3">{{ old('site_description', $settings['site_description']) }}</textarea>
            @error('site_description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="contact_email" class="form-label">{{ __('Contact Email') }}</label>
            <input type="email" class="form-control @error('contact_email') is-invalid @enderror" id="contact_email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}" required>
            @error('contact_email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Update Settings') }}</button>
    </form>
</div>
@endsection
