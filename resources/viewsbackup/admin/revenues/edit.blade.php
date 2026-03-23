@extends('layouts.dashboard')

@section('content')
<div class="container py-4">
    <h1>Edit Revenue Record #{{ $revenue->id }}</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.revenues.update', $revenue) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="total_amount" class="form-label">{{ __('Total Amount') }}</label>
                        <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount" value="{{ old('total_amount', $revenue->total_amount) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="author_percentage" class="form-label">Author Percentage (%)</label>
                        <input type="number" class="form-control" id="author_percentage" name="author_percentage" value="{{ old('author_percentage', $revenue->author_percentage) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="author_amount" class="form-label">{{ __('Author Amount') }}</label>
                        <input type="number" step="0.01" class="form-control" id="author_amount" name="author_amount" value="{{ old('author_amount', $revenue->author_amount) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="platform_amount" class="form-label">{{ __('Platform Amount') }}</label>
                        <input type="number" step="0.01" class="form-control" id="platform_amount" name="platform_amount" value="{{ old('platform_amount', $revenue->platform_amount) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">{{ __('Status') }}</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="pending" {{ $revenue->status == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="approved" {{ $revenue->status == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                        <option value="paid" {{ $revenue->status == 'paid' ? 'selected' : '' }}>{{ __('Paid') }}</option>
                    </select>
                </div>

                <hr>
                
                <h5>{{ __('Associated Info') }}</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>{{ __('Author:') }}</strong> {{ $revenue->author->name ?? 'N/A' }} (ID: {{ $revenue->author_id }})</li>
                    <li class="list-group-item"><strong>{{ __('Book:') }}</strong> {{ $revenue->book->title ?? 'N/A' }} (ID: {{ $revenue->book_id }})</li>
                    <li class="list-group-item"><strong>{{ __('Payment ID:') }}</strong> {{ $revenue->payment_id }}</li>
                    <li class="list-group-item"><strong>{{ __('Revenue Type:') }}</strong> {{ $revenue->revenue_type }}</li>
                </ul>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                    <a href="{{ route('admin.revenues.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
