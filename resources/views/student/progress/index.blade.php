@extends('layouts.student')

@section('title', 'Mon Suivi')
@section('header', 'Mon Suivi de Progression')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 border-left-primary shadow py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Livres Terminés</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBooksRead }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 border-left-success shadow py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Quiz Passés</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalQuizzesTaken }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 border-left-info shadow py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Score Moyen aux Quiz</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($averageQuizScore, 1) }}%</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Cards -->
    <div class="row">
        <div class="col-lg-4 mb-4">
            <a href="{{ route('student.progress.reading') }}" class="card text-decoration-none lift-hover h-100">
                <div class="card-body text-center">
                    <i class="fas fa-book-reader fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">Suivi de Lecture</h5>
                    <p class="card-text text-muted">Voir votre progression et le temps passé sur chaque livre.</p>
                </div>
            </a>
        </div>
        <div class="col-lg-4 mb-4">
            <a href="{{ route('reader.quizzes') }}" class="card text-decoration-none lift-hover h-100">
                <div class="card-body text-center">
                    <i class="fas fa-question-circle fa-3x text-success mb-3"></i>
                    <h5 class="card-title">Historique des Quiz</h5>
                    <p class="card-text text-muted">Consulter les résultats de toutes vos tentatives de quiz.</p>
                </div>
            </a>
        </div>
        <div class="col-lg-4 mb-4">
            <a href="{{ route('reader.badges') }}" class="card text-decoration-none lift-hover h-100">
                <div class="card-body text-center">
                    <i class="fas fa-award fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">Mes Badges</h5>
                    <p class="card-text text-muted">Voir tous les badges que vous avez débloqués.</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
