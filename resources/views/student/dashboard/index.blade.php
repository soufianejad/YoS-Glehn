@extends('layouts.dashboard')

@section('title', __('Tableau de Bord Étudiant'))
@section('header', __('Mon Tableau de Bord'))

@push('styles')
<style>
    .stat-card {
        background-color: #fff;
        border-radius: 0.75rem;
        padding: 1.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #e3e6f0;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    }
    .stat-icon {
        font-size: 2.5rem;
    }
    .book-card-progress .card-img-top {
        width: 80px;
        height: 110px;
        object-fit: cover;
    }
    .quiz-card, .badge-card {
        transition: transform 0.2s;
    }
    .quiz-card:hover, .badge-card:hover {
        transform: scale(1.03);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Bonjour,') }} {{ Auth::user()->first_name }}!</h1>
        <a href="{{ route('student.library.index') }}" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-book-reader fa-sm text-white-50"></i> {{__('Ma Bibliothèque')}}</a>
    </div>

    <!-- Key Metrics Section -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card h-100 d-flex align-items-center">
                <div class="stat-icon mr-3 text-primary"><i class="fas fa-book-check"></i></div>
                <div>
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Livres Terminés') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBooksRead }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card h-100 d-flex align-items-center">
                <div class="stat-icon mr-3 text-success"><i class="fas fa-file-signature"></i></div>
                <div>
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('Quiz Passés') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalQuizzesTaken }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card h-100 d-flex align-items-center">
                <div class="stat-icon mr-3 text-info"><i class="fas fa-star-half-alt"></i></div>
                <div>
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('Score Moyen') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($averageQuizScore, 1) }}%</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content Column -->
        <div class="col-lg-8">
            <!-- Recently Read Books Section -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Reprendre la lecture') }}</h6>
                </div>
                <div class="card-body">
                    @forelse($recentlyReadBooks as $progress)
                        <div class="card book-card-progress mb-3">
                            <div class="card-body d-flex align-items-center">
                                <img src="{{ $progress->book->cover_image_url }}" alt="{{ $progress->book->title }}" class="card-img-top rounded mr-3">
                                <div class="w-100">
                                    <h5 class="card-title mb-1">{{ $progress->book->title }}</h5>
                                    <p class="card-text text-muted small">{{ __('par') }} {{ $progress->book->author->name ?? __('Inconnu') }}</p>
                                    <div class="progress mb-1" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress->progress_percentage }}%;" aria-valuenow="{{ $progress->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">{{ round($progress->progress_percentage) }}% {{ __('terminé') }}</small>
                                        <a href="{{ route('read.book', $progress->book->slug) }}" class="btn btn-sm btn-primary">{{ __('Continuer') }} <i class="fas fa-arrow-right fa-sm"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <p class="mb-2">{{ __("Vous n'avez pas encore commencé de livre.") }}</p>
                            <a href="{{ route('student.library.index') }}" class="btn btn-primary">{{ __('Explorer la bibliothèque') }}</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Quizzes Section -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Quiz à faire') }}</h6>
                </div>
                <div class="card-body">
                    @forelse($upcomingQuizzes as $quiz)
                        <div class="d-flex justify-content-between align-items-center p-3 mb-2 bg-light border-left-warning quiz-card">
                            <div>
                                <h6 class="font-weight-bold mb-0">{{ $quiz->title }}</h6>
                                <small class="text-muted">{{ __('Livre :') }} {{ $quiz->book->title ?? __('N/A') }}</small>
                            </div>
                            <a href="{{ route('student.quiz.show', $quiz) }}" class="btn btn-warning btn-sm">{{ __('Commencer') }}</a>
                        </div>
                    @empty
                        <div class="text-center py-3">
                             <p class="mb-0 text-success"><i class="fas fa-check-circle mr-2"></i>{{ __("Vous êtes à jour ! Aucun nouveau quiz.") }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Side Column -->
        <div class="col-lg-4">
            <!-- Recommended Books Section -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Recommandations') }}</h6>
                </div>
                <div class="card-body">
                    @forelse($recommendedBooks as $book)
                        <div class="d-flex align-items-center mb-3">
                            <a href="{{ route('student.book.show', $book->slug) }}">
                                <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}" class="rounded mr-3" style="width: 50px; height: 70px; object-fit: cover;">
                            </a>
                            <div>
                                <h6 class="mb-0 small font-weight-bold"><a class="text-gray-900" href="{{ route('student.book.show', $book->slug) }}">{{ $book->title }}</a></h6>
                                <small class="text-muted">{{ $book->author->name ?? __('N/A') }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small text-center">{{ __('Aucune recommandation pour le moment.') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Earned Badges Section -->
            <div class="card shadow mb-4">
                 <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Derniers Badges') }}</h6>
                </div>
                <div class="card-body">
                     @forelse($earnedBadges as $badge)
                        <div class="d-flex align-items-center p-2 mb-2 bg-light border-left-info badge-card">
                            <i class="{{ $badge->icon }} fa-2x text-info mr-3"></i>
                            <div>
                                <h6 class="font-weight-bold mb-0">{{ $badge->name }}</h6>
                                <small class="text-muted">{{ __('Obtenu le') }} {{ $badge->pivot->earned_at->isoFormat('LL') }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small text-center">{{ __("Continuez pour gagner des badges !") }}</p>
                    @endforelse
                    <div class="text-center mt-3">
                         <a href="{{ route('student.progress.badges') }}" class="btn btn-outline-primary btn-sm">{{ __('Voir tous mes badges') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection