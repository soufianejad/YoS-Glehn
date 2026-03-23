@extends('layouts.parent')

@section('content')
<div class="container">
    <a href="{{ route('parent.dashboard') }}" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> {{ __('Back to Dashboard') }}</a>
    <h1>{{ __('Progress Report for') }} {{ $child->first_name }} {{ $child->last_name }}</h1>

    {{-- Reading Progress --}}
    <div class="card mb-4">
        <div class="card-header">
            <h4><i class="fas fa-book-open me-2"></i>{{ __('Reading Progress') }}</h4>
        </div>
        <div class="card-body">
            @if($child->readingProgress->isEmpty())
                <p>{{ __('No reading progress recorded yet.') }}</p>
            @else
                <ul class="list-group">
                    @foreach($child->readingProgress as $progress)
                        <li class="list-group-item">
                            <strong>{{ $progress->book->title }}</strong>: {{ $progress->progress }}%
                            <div class="progress mt-2">
                                <div class="progress-bar" role="progressbar" style="width: {{ $progress->progress }}%;" aria-valuenow="{{ $progress->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Audio Progress --}}
    <div class="card mb-4">
        <div class="card-header">
            <h4><i class="fas fa-headphones me-2"></i>{{ __('Listening Progress') }}</h4>
        </div>
        <div class="card-body">
            @if($child->audioProgress->isEmpty())
                <p>{{ __('No listening progress recorded yet.') }}</p>
            @else
                <ul class="list-group">
                    @foreach($child->audioProgress as $progress)
                        <li class="list-group-item">
                            <strong>{{ $progress->book->title }}</strong>: {{ $progress->progress_percentage }}%
                            <div class="progress mt-2">
                                <div class="progress-bar" role="progressbar" style="width: {{ $progress->progress_percentage }}%;" aria-valuenow="{{ $progress->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Quiz Attempts --}}
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-question-circle me-2"></i>{{ __('Quiz Attempts') }}</h4>
        </div>
        <div class="card-body">
            @if($child->quizAttempts->isEmpty())
                <p>{{ __('No quiz attempts recorded yet.') }}</p>
            @else
                <ul class="list-group">
                    @foreach($child->quizAttempts as $attempt)
                        <li class="list-group-item">
                            Quiz for <strong>{{ $attempt->quiz->book->title }}</strong>:
                            <span class="badge bg-info">{{ $attempt->score }}%</span>
                            <small class="text-muted d-block">Taken on: {{ $attempt->created_at->format('Y-m-d H:i') }}</small>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
