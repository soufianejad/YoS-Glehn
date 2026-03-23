@extends('layouts.student')

@section('title', 'Résultats du Quiz : ' . $attempt->quiz->title)

@php
    // Helper function to format seconds into a readable string
    function format_seconds($seconds) {
        if ($seconds < 60) {
            return $seconds . 's';
        }
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        if ($hours > 0) {
            return "{$hours}h {$minutes}min";
        }
        return "{$minutes}min";
    }
@endphp

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <!-- Result Header -->
            <div class="card shadow-sm mb-4 text-white @if($attempt->is_passed) bg-success @else bg-danger @endif">
                <div class="card-body text-center p-4">
                    <i class="fas @if($attempt->is_passed) fa-check-circle @else fa-times-circle @endif fa-3x mb-3"></i>
                    <h1 class="h3 mb-0">@if($attempt->is_passed) Quiz Réussi ! @else Quiz Échoué @endif</h1>
                </div>
            </div>

            <!-- Stats Summary -->
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0">Résumé de votre performance</h5></div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 col-6">
                            <div class="h5 font-weight-bold">{{ number_format($attempt->percentage, 1) }}%</div>
                            <div class="text-xs text-muted text-uppercase">Pourcentage</div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="h5 font-weight-bold">{{ $attempt->score }}</div>
                            <div class="text-xs text-muted text-uppercase">Score</div>
                        </div>
                        <div class="col-md-3 col-6 mt-3 mt-md-0">
                            <div class="h5 font-weight-bold">{{ $attempt->correct_answers }} / {{ $attempt->total_questions }}</div>
                            <div class="text-xs text-muted text-uppercase">Réponses Correctes</div>
                        </div>
                        <div class="col-md-3 col-6 mt-3 mt-md-0">
                            <div class="h5 font-weight-bold">{{ format_seconds($attempt->time_spent) }}</div>
                            <div class="text-xs text-muted text-uppercase">Temps Passé</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Answer Review -->
            @if($attempt->quiz->show_correct_answers)
                <div class="card shadow-sm">
                    <div class="card-header"><h5 class="mb-0">Revue des Réponses</h5></div>
                    <div class="card-body">
                        @foreach($attempt->quiz->questions as $question)
                            @php
                                $userAnswerIndex = $attempt->answers[$question->id] ?? null;
                                $isCorrect = $userAnswerIndex !== null && (int)$userAnswerIndex === (int)$question->correct_answer;
                            @endphp
                            <div class="border-bottom pb-3 mb-3">
                                <p class="lead mb-2"><strong>Question {{ $loop->iteration }}:</strong> {{ $question->question_text }}</p>
                                <ul class="list-group">
                                    @foreach($question->getOptionsArrayAttribute() as $index => $option)
                                        @php
                                            $isUserAnswer = $userAnswerIndex !== null && (int)$index === (int)$userAnswerIndex;
                                            $isCorrectAnswer = (int)$index === (int)$question->correct_answer;
                                            $li_class = '';
                                            $icon = '';

                                            if ($isCorrectAnswer) {
                                                $li_class = 'list-group-item-success';
                                                $icon = '<i class="fas fa-check-circle text-success me-2"></i>';
                                            }
                                            if ($isUserAnswer && !$isCorrect) {
                                                $li_class = 'list-group-item-danger';
                                                $icon = '<i class="fas fa-times-circle text-danger me-2"></i>';
                                            }
                                        @endphp
                                        <li class="list-group-item {{ $li_class }}">
                                            {!! $icon !!} {{ $option }}
                                            @if($isUserAnswer) <span class="badge bg-primary float-end">Votre réponse</span> @endif
                                        </li>
                                    @endforeach
                                </ul>
                                @if($question->explanation)
                                    <div class="mt-3 alert alert-info small">
                                        <strong>Explication :</strong> {{ $question->explanation }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="text-center mt-4">
                <a href="{{ route('student.quiz.index') }}" class="btn btn-secondary">Retour à la liste des quiz</a>
                <a href="{{ route('student.book.show', $attempt->quiz->book) }}" class="btn btn-primary">Voir le livre</a>
            </div>
        </div>
    </div>
</div>
@endsection
