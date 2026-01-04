<!-- resources/views/student/quizzes/book-quizzes.blade.php -->

@extends('layouts.student')

@section('content')
<div class="container">
    <h1>Quizzes for Book: {{ $book->title }}</h1>

    <div class="row mb-3">
        <div class="col-md-12">
            <form action="{{ route('student.quiz.book-quiz', $book) }}" method="GET" class="form-inline">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search quizzes...') }}" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">{{ __('Search') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($quizzes->isEmpty())
        <p>{{ __('No quizzes available for this book yet.') }}</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Questions') }}</th>
                    <th>{{ __('Time Limit') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quizzes as $quiz)
                    <tr>
                        <td>{{ $quiz->title }}</td>
                        <td>{{ $quiz->questions_count }}</td>
                        <td>{{ $quiz->time_limit }} minutes</td>
                        <td>
                            <form action="{{ route('student.quiz.start', $quiz) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">{{ __('Start Quiz') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $quizzes->links('pagination::bootstrap-5') }}
    @endif

    <a href="{{ route('student.book.show', $book->slug) }}" class="btn btn-secondary mt-3">{{ __('Back to Book Details') }}</a>
</div>
@endsection
