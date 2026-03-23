@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Manual Jobs') }}</h1>

    <div class="card">
        <div class="card-header">
            <h2>{{ __('Distribute Subscription Revenues') }}</h2>
        </div>
        <div class="card-body">
            <p>{{ __('This job will calculate and distribute the revenues from subscriptions for a specific month.') }}</p>
            <p>{{ __('Select the month for which you want to distribute the revenues.') }}</p>
            <form action="{{ route('admin.jobs.distribute-subscription-revenues') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="month" class="form-label">{{ __('Month') }}</label>
                            <input type="month" class="form-control" id="month" name="month" value="{{ now()->format('Y-m') }}">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">{{ __('Run Job') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
