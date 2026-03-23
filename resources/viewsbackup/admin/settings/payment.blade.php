<!-- resources/views/admin/settings/payment.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Payment Settings') }}</h1>

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="stripe_key" class="form-label">{{ __('Stripe Key') }}</label>
            <input type="text" class="form-control @error('stripe_key') is-invalid @enderror" id="stripe_key" name="stripe_key" value="{{ old('stripe_key', $settings['stripe_key']) }}">
            @error('stripe_key')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="stripe_secret" class="form-label">{{ __('Stripe Secret') }}</label>
            <input type="text" class="form-control @error('stripe_secret') is-invalid @enderror" id="stripe_secret" name="stripe_secret" value="{{ old('stripe_secret', $settings['stripe_secret']) }}">
            @error('stripe_secret')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="paypal_client_id" class="form-label">{{ __('PayPal Client ID') }}</label>
            <input type="text" class="form-control @error('paypal_client_id') is-invalid @enderror" id="paypal_client_id" name="paypal_client_id" value="{{ old('paypal_client_id', $settings['paypal_client_id']) }}">
            @error('paypal_client_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Update Settings') }}</button>
    </form>
</div>
@endsection
