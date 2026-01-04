@extends('layouts.student')

@section('title', 'Détails du Quiz')
@section('header', 'Prêt pour le quiz ?')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm text-center">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ $quiz->title }}</h4>
                </div>
                <div class="card-body p-4">
                    <h5 class="card-title">Associé au livre : <strong>{{ $quiz->book->title ?? 'N/A' }}</strong></h5>
                    <p class="text-muted">{{ $quiz->description }}</p>
                    
                    <hr>

                    <div class="row text-start my-4">
                        <div class="col-md-6 mb-3">
                            <p class="mb-0"><i class="fas fa-question-circle text-primary me-2"></i> <strong>Questions :</strong> {{ $quiz->questions_count }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-0"><i class="fas fa-bullseye text-success me-2"></i> <strong>Score pour réussir :</strong> {{ $quiz->pass_score }}%</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <p class="mb-0"><i class="fas fa-clock text-warning me-2"></i> <strong>Temps imparti :</strong> {{ $quiz->time_limit }} minutes</p>
                        </div>
                    </div>

                    <form action="{{ route('student.quiz.start', $quiz) }}" method="GET"> {{-- Changed to GET for simplicity if no data is posted --}}
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-play-circle me-2"></i> Commencer le Quiz
                        </button>
                    </form>
                </div>
                <div class="card-footer text-muted">
                    <a href="{{ route('student.quiz.index') }}" class="btn btn-sm btn-outline-secondary">Retour à la liste des quiz</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
