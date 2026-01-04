<!-- resources/views/admin/revenues/payouts/create.blade.php -->

@extends('@extends('layouts.dashboard')outs.dashboard')')

@section('content')
<div class="container">
    <h1>Create Payout for {{ $author->name }}</h1>

    <form action="{{ route('admin.revenues.payouts.store') }}" method="POST">
        @csrf

        <input type="hidden" name="author_id" value="{{ $author->id }}">

        <div class="mb-3">
            <label for="amount" class="form-label">{{ __('Amount') }}</label>
            <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" required>
            @error('amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="currency" class="form-label">{{ __('Currency') }}</label>
            <input type="text" class="form-control @error('currency') is-invalid @enderror" id="currency" name="currency" value="{{ old('currency', 'USD') }}" required>
            @error('currency')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="payment_method" class="form-label">{{ __('Payment Method') }}</label>
            <input type="text" class="form-control @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" value="{{ old('payment_method') }}" required>
            @error('payment_method')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="payment_details" class="form-label">Payment Details (JSON)</label>
            <textarea class="form-control @error('payment_details') is-invalid @enderror" id="payment_details" name="payment_details" rows="3">{{ old('payment_details') }}</textarea>
            @error('payment_details')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="period_start" class="form-label">{{ __('Period Start') }}</label>
            <input type="date" class="form-control @error('period_start') is-invalid @enderror" id="period_start" name="period_start" value="{{ old('period_start') }}" required>
            @error('period_start')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="period_end" class="form-label">{{ __('Period End') }}</label>
            <input type="date" class="form-control @error('period_end') is-invalid @enderror" id="period_end" name="period_end" value="{{ old('period_end') }}" required>
            @error('period_end')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Create Payout') }}</button>
    </form>
</div>
@endsection
