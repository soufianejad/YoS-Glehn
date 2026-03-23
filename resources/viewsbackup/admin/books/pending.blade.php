<!-- resources/views/admin/books/pending.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Pending Books') }}</h1>

    <table class="table">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Author') }}</th>
                <th>{{ __('Category') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($books as $book)
                <tr>
                    <td>{{ $book->id }}</td>
                    <td>{{ $book->title }}</td>
                    <td>{{ $book->author->name ?? __('N/A') }}</td>
                    <td>{{ $book->category->name ?? __('N/A') }}</td>
                    <td>
                        <a href="{{ route('admin.books.show', $book) }}" class="btn btn-sm btn-info">{{ __('View') }}</a>
                        <form action="{{ route('admin.books.approve', $book) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('{{ __("Approve this book?") }}')">{{ __('Approve') }}</button>
                        </form>
                        <form action="{{ route('admin.books.reject', $book) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __("Reject this book?") }}')">{{ __('Reject') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $books->links('pagination::bootstrap-5') }}
</div>
@endsection
