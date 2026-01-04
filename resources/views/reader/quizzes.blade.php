@extends('layouts.dashboard')

@section('title', 'Mon Historique de Quiz')
@section('header', 'Mon Historique de Quiz')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Toutes vos tentatives de quiz, regroupées par livre.</h6>
        </div>
        <div class="card-body">
            @if($groupedAttempts->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Vous n'avez encore tenté aucun quiz.</p>
                </div>
            @else
                <div class="accordion" id="quizAccordion">
                    @foreach($groupedAttempts as $bookTitle => $attempts)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-{{ $loop->iteration }}">
                                <button class="accordion-button @if(!$loop->first) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $loop->iteration }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse-{{ $loop->iteration }}">
                                    <span class="font-weight-bold">{{ $bookTitle ?: 'Quiz non associé à un livre' }}</span>
                                    <span class="badge bg-secondary ms-2">{{ $attempts->count() }} {{ Str::plural('tentative', $attempts->count()) }}</span>
                                </button>
                            </h2>
                            <div id="collapse-{{ $loop->iteration }}" class="accordion-collapse collapse @if($loop->first) show @endif" aria-labelledby="heading-{{ $loop->iteration }}" data-bs-parent="#quizAccordion">
                                <div class="accordion-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Quiz</th>
                                                    <th>Date</th>
                                                    <th class="text-center">Score</th>
                                                    <th class="text-center">Statut</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($attempts as $attempt)
                                                    <tr>
                                                        <td>{{ $attempt->quiz->title }}</td>
                                                        <td>{{ $attempt->completed_at->format('d/m/Y H:i') }}</td>
                                                        <td class="text-center">{{ round($attempt->percentage) }}%</td>
                                                        <td class="text-center">
                                                            @if($attempt->is_passed)
                                                                <span class="badge bg-success">Réussi</span>
                                                            @else
                                                                <span class="badge bg-danger">Échoué</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="{{ route('student.quiz.results', $attempt) }}" class="btn btn-sm btn-info">Voir les détails</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
