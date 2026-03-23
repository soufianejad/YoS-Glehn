@extends('layouts.app')

@section('title', __('Notification Preferences'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h1 class="h3 mb-0">{{ __('Notification Preferences') }}</h1>
                    <p class="text-muted mb-0">{{ __('Manage how you receive notifications.') }}</p>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('profile.notifications.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Notification Type') }}</th>
                                    <th class="text-center">{{ __('In-App') }}</th>
                                    <th class="text-center">{{ __('Email') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notificationTypes as $type => $label)
                                    <tr>
                                        <td>
                                            <strong>{{ $label }}</strong>
                                            <p class="text-muted text-sm mb-0">
                                                {{-- A description could go here --}}
                                            </p>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" name="prefs[{{ $type }}][site]" value="1"
                                                    {{-- Default to true if no preference is set --}}
                                                    {{ (isset($preferences[$type]['site']) && $preferences[$type]['site'] === false) ? '' : 'checked' }}>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" name="prefs[{{ $type }}][email]" value="1"
                                                    {{-- Default to true if no preference is set --}}
                                                    {{ (isset($preferences[$type]['email']) && $preferences[$type]['email'] === false) ? '' : 'checked' }}>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary">{{ __('Save Preferences') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- Add Bootstrap 5 form-switch styles if not globally available --}}
<style>
.form-switch .form-check-input {
    width: 2em;
    margin-left: -2.5em;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba(0,0,0,0.25)'/%3e%3c/svg%3e");
    background-position: left center;
    border-radius: 2em;
    transition: background-position .15s ease-in-out;
}
</style>
@endpush
