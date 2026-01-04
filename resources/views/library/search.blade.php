<!-- resources/views/library/search.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Search Results for "{{ $query }}"</h1>

    <div class="row mb-3">
        <div class="col-md-12">
            <form action="{{ route('library.search') }}" method="GET" class="form-inline">
                <div class="input-group">
                    <input type="text" name="query" class="form-control" placeholder="{{ __('Search books...') }}" value="{{ request('query') }}">
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
                @foreach($categories as $category)
                    <li class="list-group-item">
                        <a href="{{ route('library.category', $category->slug) }}">{{ $category->name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="col-md-9">
            @if($books->isEmpty())
                <p>{{ __('No books found matching your search criteria.') }}</p>
            @else
                <div class="row">
                    @foreach($books as $book)
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                @auth
                                    <form action="{{ route('favorites.toggle', $book) }}" method="POST" class="position-absolute top-0 end-0 m-2 favorite-form">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                            @if(auth()->user()->favorites->contains($book->id))
                                                <i class="fas fa-heart"></i>
                                            @else
                                                <i class="far fa-heart"></i>
                                            @endif
                                        </button>
                                    </form>
                                @endauth
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
            @endif
        </div>
    </div>
</div>
@endsection
