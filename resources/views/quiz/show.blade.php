@extends('layouts.dashboard')

@section('title', 'Quiz for ' . $book->title)
@section('header', 'Quiz: ' . $book->title)

@section('content')
<div class="container">
    <h1 class="mb-4">{{ $quiz->title }}</h1>

    <form action="{{ route('quiz.submit', $book) }}" method="POST">
        @csrf

        @foreach($quiz->questions as $question)
            <div class="card mb-3">
                <div class="card-header fw-bold">Question {{ $loop->iteration }}</div>
                <div class="card-body">
                    <p class="card-text fs-5">{{ $question->question_text }}</p>
                    
                    @if($question->options)
                        <div class="ms-3">
                            @foreach($question->options as $key => $optionText)
                                <div class="form-check fs-5">
                                    <input class="form-check-input" type="radio" name="questions[{{ $question->id }}]" id="q{{ $question->id }}_o{{ $key }}" value="{{ $key }}" required>
                                    <label class="form-check-label" for="q{{ $question->id }}_o{{ $key }}">
                                        {{ $optionText }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
        @endforeach

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary btn-lg">{{ __('Submit Quiz') }}</button>
        </div>
    </form>
</div>
@endsection
