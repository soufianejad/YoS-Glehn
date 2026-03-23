@extends('layouts.dashboard')

@section('title', 'Quiz Result')
@section('header', 'Quiz Result: ' . $attempt->quiz->book->title)

@section('content')
<div class="card">
    <div class="card-body text-center">
        <h2 class="card-title">{{ __('Votre Score') }}</h2>
        <h1 class="display-1 {{ $attempt->is_passed ? 'text-success' : 'text-danger' }}">{{ round($attempt->score) }}%</h1>
        @if($attempt->is_passed)
            <p class="lead text-success">{{ __('Félicitations, vous avez réussi !') }}</p>
        @else
            <p class="lead text-danger">{{ __('Vous n\'avez pas réussi. Continuez d\'essayer !') }}</p>
        @endif
        <hr>
        <a href="{{ route('book.show', $attempt->quiz->book->slug) }}" class="btn btn-secondary">{{ __('Retour au Livre') }}</a>
        <a href="{{ route('quiz.show', $attempt->quiz->book) }}" class="btn btn-primary">{{ __('Réessayer') }}</a>
    </div>
</div>
@endsection
