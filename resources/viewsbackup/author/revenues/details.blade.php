<!-- resources/views/author/revenues/details.blade.php -->

@extends('layouts.author')

@section('content')
<div class="container">
    <h1>{{ __('Revenue Details') }}</h1>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('Book') }}</th>
                <th>{{ __('Payment ID') }}</th>
                <th>{{ __('Total Amount') }}</th>
                <th>{{ __('My Share') }}</th>
                <th>{{ __('Platform Share') }}</th>
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
                    <td>{{ $revenue->payment->transaction_id ?? 'N/A' }}</td>
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
</div>
@endsection
