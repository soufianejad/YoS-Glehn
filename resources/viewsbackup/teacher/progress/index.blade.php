@extends('layouts.dashboard')

@section('title', 'Suivi de Progression')
@section('header', 'Suivi de la classe : ' . $class->name)

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <p class="m-0">Progression de lecture pour chaque élève.</p>
        </div>
        <div class="card-body">
            @if($students->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Il n'y a aucun élève dans cette classe pour le moment.</p>
                </div>
            @else
                <div class="accordion" id="studentsAccordion">
                    @foreach($students as $student)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $student->id }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $student->id }}" aria-expanded="false" aria-controls="collapse{{ $student->id }}">
                                    {{ $student->name }}
                                </button>
                            </h2>
                            <div id="collapse{{ $student->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $student->id }}" data-bs-parent="#studentsAccordion">
                                <div class="accordion-body">
                                    @php
                                        $studentQuizAttempts = $quizAttempts->get($student->id);
                                    @endphp

                                    <h5 class="mb-3">Progression de Lecture</h5>
                                    @if($assignedBooks->isNotEmpty())
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Livre Assigné</th>
                                                    <th style="width: 40%;">Progression</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($assignedBooks as $book)
                                                    @php
                                                        $progress = $student->readingProgress->where('book_id', $book->id)->first();
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $book->title }}</td>
                                                        <td>
                                                            @if($progress)
                                                                <div class="progress" style="height: 20px;">
                                                                    <div class="progress-bar" role="progressbar" style="width: {{ $progress->percentage }}%;" aria-valuenow="{{ $progress->percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                                        {{ round($progress->percentage) }}%
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <span class="text-muted fst-italic">Pas encore commencé</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p class="text-muted mb-0">Aucun livre n'a été assigné à cette classe.</p>
                                    @endif

                                    <hr class="my-4">

                                    <h5 class="mb-3">Tentatives de Quiz</h5>
                                    @if($studentQuizAttempts && $studentQuizAttempts->count() > 0)
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Quiz</th>
                                                    <th class="text-center">Score</th>
                                                    <th class="text-center">Statut</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($studentQuizAttempts as $attempt)
                                                    <tr>
                                                        <td>{{ $attempt->quiz->title }} <br> <small class="text-muted">{{ $attempt->quiz->book->title }}</small></td>
                                                        <td class="text-center align-middle">{{ round($attempt->percentage) }}%</td>
                                                        <td class="text-center align-middle">
                                                            @if($attempt->is_passed)
                                                                <span class="badge bg-success">Réussi</span>
                                                            @else
                                                                <span class="badge bg-danger">Échoué</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <a href="{{ route('teacher.progress.quiz-attempt', $attempt) }}" class="btn btn-sm btn-info">Voir les détails</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p class="text-muted mb-0">Aucun quiz n'a été tenté par cet élève.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="card-footer">
            <a href="{{ route('teacher.classes.show', $class) }}" class="btn btn-light btn-sm">
                &larr; Retour à la classe
            </a>
        </div>
    </div>
</div>
@endsection