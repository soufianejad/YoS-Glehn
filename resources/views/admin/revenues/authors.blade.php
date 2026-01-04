<!-- resources/views/admin/revenues/authors.blade.php -->

@extends('@extends('layouts.dashboard')outs.dashboard')')

@section('content')
<div class="container">
    <h1>{{ __('Author Revenues') }}</h1>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Total Earnings') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($authors as $author)
                <tr>
                    <td>{{ $author->id }}</td>
                    <td>{{ $author->name }}</td>
                    <td>{{ $author->email }}</td>
                    <td>${{ number_format($author->revenues_sum_author_amount ?? 0, 2) }}</td>
                    <td>
                        <a href="{{ route('admin.revenues.author-detail', $author) }}" class="btn btn-sm btn-info">{{ __('View Details') }}</a>
                        <a href="{{ route('admin.revenues.payouts.create', $author) }}" class="btn btn-sm btn-success">{{ __('Create Payout') }}</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $authors->links('pagination::bootstrap-5') }}
</div>
@endsection
