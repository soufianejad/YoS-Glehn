<!-- resources/views/library/category.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Books in {{ $category->name }}</h1>

    <div class="row mb-3">
        <div class="col-md-12">
            <form action="{{ route('library.category', $category) }}" method="GET" class="form-inline">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search books...') }}" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">{{ __('Search') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <h3>{{ __('Categories') }}</h3>
            <ul class="list-group">
                @foreach($categories as $cat)
                    <li class="list-group-item @if($cat->id === $category->id) active @endif">
                        <a href="{{ route('library.category', $cat->slug) }}">{{ $cat->name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="col-md-9">
            <div class="row">
                @foreach($books as $book)
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default_book_cover.png') }}" class="card-img-top" alt="{{ $book->title }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $book->title }}</h5>
                                <p class="card-text">{{ Str::limit($book->description, 100) }}</p>
                                <a href="{{ route('book.show', $book->slug) }}" class="btn btn-primary">{{ __('View Book') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            {{ $books->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
