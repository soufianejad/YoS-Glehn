<!-- resources/views/admin/revenues/payouts/index.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Author Payouts') }}</h1>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('Author') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Period Start') }}</th>
                <th>{{ __('Period End') }}</th>
                <th>{{ __('Processed At') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payouts as $payout)
                <tr>
                    <td>{{ $payout->id }}</td>
                    <td>{{ $payout->author->name ?? 'N/A' }}</td>
                    <td>${{ $payout->amount }} {{ $payout->currency }}</td>
                    <td>{{ $payout->status }}</td>
                    <td>{{ \Carbon\Carbon::parse($payout->period_start)->format('Y-m-d') }}</td>
                    <td>{{ \Carbon\Carbon::parse($payout->period_end)->format('Y-m-d') }}</td>
                    <td>{{ $payout->processed_at ? \Carbon\Carbon::parse($payout->processed_at)->format('Y-m-d H:i') : 'N/A' }}</td>
                                        <td>
                        <a href="{{ route('admin.revenues.payouts.show', $payout) }}" class="btn btn-sm btn-info">{{ __('View') }}</a>
                        @if($payout->isPending())
                            <form action="{{ route('admin.revenues.payouts.confirm', $payout) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">{{ __('Confirm') }}</button>
                            </form>
                            <form action="{{ route('admin.revenues.payouts.cancel', $payout) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">{{ __('Cancel') }}</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $payouts->links('pagination::bootstrap-5') }}
</div>
@endsection
