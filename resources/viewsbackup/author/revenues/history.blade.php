<!-- resources/views/author/revenues/history.blade.php -->

@extends('layouts.author')

@section('content')
<div class="container">
    <h1>{{ __('Payout History') }}</h1>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Period Start') }}</th>
                <th>{{ __('Period End') }}</th>
                <th>{{ __('Processed At') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payouts as $payout)
                <tr>
                    <td>{{ $payout->id }}</td>
                    <td>${{ $payout->amount }} {{ $payout->currency }}</td>
                    <td>{{ $payout->status }}</td>
                    <td>{{ $payout->period_start ? \Carbon\Carbon::parse($payout->period_start)->format('M d, Y') : 'N/A' }}</td>
                    <td>{{ $payout->period_end ? \Carbon\Carbon::parse($payout->period_end)->format('M d, Y') : 'N/A' }}</td>
                    <td>{{ $payout->processed_at ? \Carbon\Carbon::parse($payout->processed_at)->format('M d, Y H:i') : 'N/A' }}</td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $payouts->links('pagination::bootstrap-5') }}
</div>
@endsection
