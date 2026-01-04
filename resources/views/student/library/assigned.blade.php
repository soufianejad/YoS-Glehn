<!-- resources/views/student/library/assigned.blade.php -->

@extends('layouts.student')

@section('content')
<div class="container">
    <h1>{{ __('Assigned Books') }}</h1>

    <div class="row mb-3">
        <div class="col-md-12">
            <form action="{{ route('student.library.assigned') }}" method="GET" class="form-inline">
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
                @foreach($categories as $category)
                    <li class="list-group-item">
                        <a href="{{ route('student.library.category', $category->slug) }}">{{ $category->name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="col-md-9">
            @if($books->isEmpty())
                <p>{{ __('No books have been assigned to your classes yet.') }}</p>
            @else
                <div class="row">
                    @foreach($books as $book)
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default_book_cover.png') }}" class="card-img-top" alt="{{ $book->title }}">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $book->title }}</h5>
                                                                    <p class="card-text">{{ Str::limit($book->description, 100) }}</p>
                                    
                                                                    @auth
                                                                        @if($book->readingProgress->isNotEmpty() && $book->readingProgress->first()->progress_percentage > 0 && $book->readingProgress->first()->progress_percentage < 100)
                                                                            <div class="progress mb-2" style="height: 8px;">
                                                                                <div class="progress-bar" role="progressbar" style="width: {{ $book->readingProgress->first()->progress_percentage }}%;" aria-valuenow="{{ $book->readingProgress->first()->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                            </div>
                                                                            <p class="card-text"><small class="text-muted">{{ __('Progress:') }} {{ number_format($book->readingProgress->first()->progress_percentage, 0) }}%</small></p>
                                                                            <a href="{{ route('student.book.read', $book->slug) }}" class="btn btn-sm btn-success mt-2">{{ __('Continue Reading') }}</a>
                                                                        @elseif($book->readingProgress->isNotEmpty() && $book->readingProgress->first()->progress_percentage == 100)
                                                                            <p class="card-text"><small class="text-success">{{ __('Completed!') }}</small></p>
                                                                            <a href="{{ route('student.book.read', $book->slug) }}" class="btn btn-sm btn-info mt-2">{{ __('Read Again') }}</a>
                                                                        @endif
                                                                    @endauth
                                    
                                                                    <a href="{{ route('student.book.show', $book->slug) }}" class="btn btn-primary">{{ __('View Book') }}</a>                                </div>
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
