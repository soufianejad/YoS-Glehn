@extends('layouts.dashboard')

@section('title', 'Mon Espace - Étudiant')

@section('content')

<div class="container-fluid py-4">
    
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">
                                👋 Bienvenue, {{ $user->first_name }} !
                            </h2>
                            <p class="mb-0">
                                <i class="bi bi-building"></i> {{ $school->name }}
                                @if($user->classes->count() > 0)
                                    - {{ $user->classes->pluck('name')->join(', ') }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <h3 class="mb-0">{{ $stats['total_points'] }} points</h3>
                            <small>{{ __('🏆 Continue comme ça !') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-2" style="font-size: 3rem;">
                        <i class="bi bi-book-fill"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['books_read'] }}</h3>
                    <p class="text-muted mb-0">{{ __('Livres Lus') }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-2" style="font-size: 3rem;">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                    <h3 class="mb-0">{{ gmdate('H:i', $stats['reading_time']) }}</h3>
                    <p class="text-muted mb-0">{{ __('Temps de Lecture') }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-warning mb-2" style="font-size: 3rem;">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['quizzes_passed'] }}</h3>
                    <p class="text-muted mb-0">{{ __('Quiz Réussis') }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-danger mb-2" style="font-size: 3rem;">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <h3 class="mb-0">{{ $stats['badges_earned'] }}</h3>
                    <p class="text-muted mb-0">{{ __('Badges Gagnés') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="row">
        
        <!-- Left Column -->
        <div class="col-lg-8 mb-4">
            
            <!-- Recommended Books -->
            @if($recommendedBooks->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-star-fill text-warning"></i> {{ __('Recommandés par vos professeurs') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($recommendedBooks->take(3) as $book)
                                <div class="col-md-4 mb-3">
                                    <a href="{{ route('student.library.show', $book->slug) }}" class="text-decoration-none">
                                        <img src="{{ $book->cover_url }}" class="img-fluid rounded shadow-sm mb-2" alt="{{ $book->title }}">
                                        <h6 class="text-truncate">{{ $book->title }}</h6>
                                        <small class="text-muted">{{ $book->author->full_name }}</small>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('student.library.recommended') }}" class="btn btn-sm btn-outline-primary">
                            Voir tous les livres recommandés <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Continue Reading -->
            @if($continueReading->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-bookmark-fill text-primary"></i> {{ __('Reprendre la lecture') }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach($continueReading as $progress)
                            <div class="d-flex mb-3 pb-3 border-bottom">
                                <img src="{{ $progress->book->cover_url }}" class="rounded me-3" width="60" height="80" style="object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $progress->book->title }}</h6>
                                    <small class="text-muted d-block mb-2">{{ $progress->book->author->full_name }}</small>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar" style="width: {{ $progress->progress_percentage }}%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">{{ round($progress->progress_percentage) }}% - Page {{ $progress->current_page }}/{{ $progress->total_pages }}</small>
                                        <a href="{{ route('student.library.read', $progress->book->slug) }}" class="btn btn-sm btn-primary">
                                            Continuer <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Continue Listening -->
            @if($continueListening->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-headphones text-success"></i> {{ __("Continuer l'écoute") }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach($continueListening as $progress)
                            <div class="d-flex mb-3 pb-3 border-bottom">
                                <img src="{{ $progress->book->cover_url }}" class="rounded me-3" width="60" height="80" style="object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $progress->book->title }}</h6>
                                    <small class="text-muted d-block mb-2">{{ $progress->book->author->full_name }}</small>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: {{ $progress->progress_percentage }}%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">{{ round($progress->progress_percentage) }}% - {{ gmdate('H:i:s', $progress->current_position) }}</small>
                                        <a href="{{ route('student.library.listen', $progress->book->slug) }}" class="btn btn-sm btn-success">
                                            Écouter <i class="bi bi-play-fill"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            
            <!-- Recent Badges -->
            @if($recentBadges->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-trophy-fill text-warning"></i> {{ __('Badges Récents') }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach($recentBadges as $badge)
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                     style="width: 50px; height: 50px; font-size: 1.5rem;">
                                    <i class="bi bi-award"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $badge->name }}</h6>
                                    <small class="text-muted">{{ $badge->description }}</small>
                                </div>
                            </div>
                        @endforeach
                        <a href="{{ route('student.progress.badges') }}" class="btn btn-sm btn-outline-warning w-100">
                            Voir tous mes badges <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Available Quizzes -->
            @if($availableQuizzes->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-question-circle-fill text-info"></i> {{ __('Quiz Disponibles') }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach($availableQuizzes as $quiz)
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                <div>
                                    <h6 class="mb-1">{{ $quiz->book->title }}</h6>
                                    <small class="text-muted">{{ $quiz->questions_count }} questions</small>
                                </div>
                                <a href="{{ route('student.quiz.show', $quiz) }}" class="btn btn-sm btn-info">
                                    {{ __('Faire le quiz') }}
                                </a>
                            </div>
                        @endforeach
                        <a href="{{ route('student.quiz.index') }}" class="btn btn-sm btn-outline-info w-100">
                            Voir tous les quiz <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Quick Links -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-link-45deg"></i> {{ __('Liens Rapides') }}</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('student.library.index') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-book"></i> {{ __('Bibliothèque') }}
                    </a>
                    <a href="{{ route('student.progress.index') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-graph-up"></i> {{ __('Ma Progression') }}
                    </a>
                    <a href="{{ route('student.quiz.index') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-question-circle"></i> {{ __('Mes Quiz') }}
                    </a>
                    <a href="{{ route('student.school.info') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-building"></i> {{ __('Mon École') }}
                    </a>
                    <a href="{{ route('student.progress.leaderboard') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-trophy"></i> {{ __('Classement') }}
                    </a>
                </div>
            </div>

            <!-- School Info -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3"><i class="bi bi-building"></i> {{ __('Mon École') }}</h6>
                    <img src="{{ $school->logo ? asset('storage/' . $school->logo) : asset('assets/images/school-default.png') }}" 
                         class="img-fluid rounded mb-3" alt="{{ $school->name }}">
                    <h5>{{ $school->name }}</h5>
                    <p class="text-muted small mb-0">{{ $school->city }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection