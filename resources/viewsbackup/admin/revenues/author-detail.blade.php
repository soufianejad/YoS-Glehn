<!-- resources/views/admin/revenues/author-detail.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>Revenue Details for {{ $author->name }}</h1>

    <div class="card mb-4">
        <div class="card-header">{{ __('Summary') }}</div>
        <div class="card-body">
            <p><strong>{{ __('Total Earnings:') }}</strong> ${{ number_format($totalEarnings, 2) }}</p>
        </div>
    </div>

    <h2>{{ __('All Revenues') }}</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('Book') }}</th>
                <th>{{ __('Total Amount') }}</th>
                <th>{{ __('Author Amount') }}</th>
                <th>{{ __('Platform Amount') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Paid At') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenues as $revenue)
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
            @endforeach
        </tbody>
    </table>

    {{ $revenues->links('pagination::bootstrap-5') }}

    <a href="{{ route('admin.revenues.authors') }}" class="btn btn-secondary mt-3">{{ __('Back to Authors') }}</a>
</div>
@endsection
