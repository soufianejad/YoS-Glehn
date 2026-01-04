<!-- resources/views/author/revenues/monthly-report.blade.php -->

@extends('layouts.author')

@section('content')
<div class="container">
    <h1>Monthly Revenue Report ({{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }})</h1>

    <div class="card mb-4">
        <div class="card-header">{{ __('Summary') }}</div>
        <div class="card-body">
            <p><strong>{{ __('Total Monthly Earnings:') }}</strong> ${{ number_format($totalMonthlyRevenue, 2) }}</p>
            <p><strong>{{ __('Number of Revenue Entries:') }}</strong> {{ $revenues->count() }}</p>
        </div>
    </div>

    <h2>{{ __('All Revenue Entries this Month') }}</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('Book') }}</th>
                <th>{{ __('Total Amount') }}</th>
                <th>{{ __('My Share') }}</th>
                <th>{{ __('Platform Share') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Paid At') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($revenues as $revenue)
                <tr>
                    <td>{{ $revenue->id }}</td>
                    <td>{{ $revenue->book->title ?? 'N/A' }}</td>
                    <td>${{ $revenue->total_amount }}</td>
                    <td>${{ $revenue->author_amount }}</td>
                    <td>${{ $revenue->platform_amount }}</td>
                    <td>{{ $revenue->revenue_type }}</td>
                    <td>{{ $revenue->status }}</td>
                    <td>{{ $revenue->paid_at ? $revenue->paid_at->format('Y-m-d H:i') : 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">{{ __('No revenue entries for this month.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
