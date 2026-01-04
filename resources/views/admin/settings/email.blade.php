<!-- resources/views/admin/settings/email.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Email Settings') }}</h1>

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="mail_mailer" class="form-label">{{ __('Mail Mailer') }}</label>
            <input type="text" class="form-control @error('mail_mailer') is-invalid @enderror" id="mail_mailer" name="mail_mailer" value="{{ old('mail_mailer', $settings['mail_mailer']) }}">
            @error('mail_mailer')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="mail_host" class="form-label">{{ __('Mail Host') }}</label>
            <input type="text" class="form-control @error('mail_host') is-invalid @enderror" id="mail_host" name="mail_host" value="{{ old('mail_host', $settings['mail_host']) }}">
            @error('mail_host')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="mail_port" class="form-label">{{ __('Mail Port') }}</label>
            <input type="text" class="form-control @error('mail_port') is-invalid @enderror" id="mail_port" name="mail_port" value="{{ old('mail_port', $settings['mail_port']) }}">
            @error('mail_port')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="mail_username" class="form-label">{{ __('Mail Username') }}</label>
            <input type="text" class="form-control @error('mail_username') is-invalid @enderror" id="mail_username" name="mail_username" value="{{ old('mail_username', $settings['mail_username']) }}">
            @error('mail_username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="mail_password" class="form-label">{{ __('Mail Password') }}</label>
            <input type="password" class="form-control @error('mail_password') is-invalid @enderror" id="mail_password" name="mail_password" value="{{ old('mail_password', $settings['mail_password']) }}">
            @error('mail_password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="mail_encryption" class="form-label">{{ __('Mail Encryption') }}</label>
            <input type="text" class="form-control @error('mail_encryption') is-invalid @enderror" id="mail_encryption" name="mail_encryption" value="{{ old('mail_encryption', $settings['mail_encryption']) }}">
            @error('mail_encryption')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="mail_from_address" class="form-label">{{ __('Mail From Address') }}</label>
            <input type="email" class="form-control @error('mail_from_address') is-invalid @enderror" id="mail_from_address" name="mail_from_address" value="{{ old('mail_from_address', $settings['mail_from_address']) }}">
            @error('mail_from_address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="mail_from_name" class="form-label">{{ __('Mail From Name') }}</label>
            <input type="text" class="form-control @error('mail_from_name') is-invalid @enderror" id="mail_from_name" name="mail_from_name" value="{{ old('mail_from_name', $settings['mail_from_name']) }}">
            @error('mail_from_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Update Settings') }}</button>
    </form>
</div>
@endsection
