@extends('layouts.dashboard')

@section('title', 'Quiz Result')
@section('header', 'Quiz Result: ' . $attempt->quiz->book->title)

@section('content')
<div class="card">
    <div class="card-body text-center">
        <h2 class="card-title">{{ __('Your Score') }}</h2>
        <h1 class="display-1 {{ $attempt->is_passed ? 'text-success' : 'text-danger' }}">{{ round($attempt->score) }}%</h1>
        @if($attempt->is_passed)
            <p class="lead text-success">{{ __('Congratulations, you passed!') }}</p>
        @else
            <p class="lead text-danger">{{ __('You did not pass. Keep trying!') }}</p>
        @endif
        <hr>
        <a href="{{ route('book.show', $attempt->quiz->book->slug) }}" class="btn btn-secondary">{{ __('Back to Book') }}</a>
        <a href="{{ route('quiz.show', $attempt->quiz->book) }}" class="btn btn-primary">{{ __('Try Again') }}</a>
    </div>
</div>
@endsection
