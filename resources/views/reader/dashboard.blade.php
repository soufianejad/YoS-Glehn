@extends('layouts.dashboard')

@section('title', __('Tableau de Bord Lecteur'))
@section('header', __('Tableau de Bord'))

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
        font-size: 3rem;
        color: #4e73df;
    }
    .book-card-progress .card-img-top {
        width: 100px;
        height: 140px;
        object-fit: cover;
    }
    .recommendation-card .card-img-top {
        height: 180px;
        object-fit: cover;
    }
    .star-rating {
        color: #ffc107;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Bonjour,') }} {{ $user->first_name }}!</h1>
        <a href="{{ route('library.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-book-open fa-sm text-white-50"></i> {{__('Explorer la bibliothèque')}}</a>
    </div>

    <!-- Stats Section -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card h-100 d-flex align-items-center">
                <div class="stat-icon mr-3">
                    <i class="fas fa-book-reader"></i>
                </div>
                <div>
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Livres Terminés') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedBooks }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card h-100 d-flex align-items-center">
                <div class="stat-icon mr-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('Heures de Lecture') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalReadingTime }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card h-100 d-flex align-items-center">
                <div class="stat-icon mr-3">
                    <i class="fas fa-trophy"></i>
                </div>
                <div>
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Badges Obtenus') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $badgesCount }}</div>
                </div>
            </div>
        </div>
    </div>


    <!-- Content Row -->
    <div class="row">

        <!-- Continue Reading Section -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Reprendre la lecture') }}</h6>
                </div>
                <div class="card-body">
                    @forelse ($continueReading as $progress)
                        <div class="card book-card-progress mb-3">
                            <div class="card-body d-flex align-items-center">
                                <img src="{{ $progress->book->cover_image_url }}" alt="{{ $progress->book->title }}" class="card-img-top rounded mr-3">
                                <div class="w-100">
                                    <h5 class="card-title mb-1">{{ $progress->book->title }}</h5>
                                    <p class="card-text text-muted small">{{ __('par') }} {{ $progress->book->author->name ?? __('Inconnu') }}</p>
                                    <div class="progress mb-1" style="height: 8px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $progress->progress_percentage }}%;" aria-valuenow="{{ $progress->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">{{ round($progress->progress_percentage) }}% {{ __('terminé') }}</small>
                                        <a href="{{ route('read.book', $progress->book) }}" class="btn btn-sm btn-primary">{{ __('Continuer') }} <i class="fas fa-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <p>{{ __("Vous n'avez pas encore commencé de livre.") }}</p>
                            <a href="{{ route('library.index') }}" class="btn btn-primary">{{ __('Explorer la bibliothèque') }}</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recommendations Section -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Recommandé pour vous') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse ($recommendations as $book)
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="card recommendation-card h-100 border-0">
                                    <a href="{{ route('book.show', $book) }}">
                                        <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}" class="card-img-top rounded shadow-sm">
                                    </a>
                                    <div class="card-body p-2 text-center">
                                         <h6 class="card-title small font-weight-bold mt-2"><a href="{{ route('book.show', $book) }}" class="text-gray-800">{{ Str::limit($book->title, 25) }}</a></h6>
                                        <p class="card-text small text-muted mb-1">{{ $book->author->name }}</p>
                                        <div class="star-rating small">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $book->average_rating ? 'text-warning' : 'text-gray-300' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                             <div class="col">
                                <p class="text-center text-muted">{{ __('Aucune recommandation pour le moment.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Column -->
        <div class="col-lg-4 mb-4">
            <!-- Latest Achievement Section -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Dernier Succès') }}</h6>
                </div>
                <div class="card-body text-center">
                    @if ($latestBadge)
                        <i class="{{ $latestBadge->icon }} fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">{{ $latestBadge->name }}</h5>
                        <p class="card-text small">{{ $latestBadge->description }}</p>
                        <hr>
                        <p class="card-text text-muted small">
                            {{ __('Obtenu le:') }} {{ \Carbon\Carbon::parse($latestBadge->pivot->earned_at)->isoFormat('LL') }}
                        </p>
                        <a href="{{ route('reader.badges') }}" class="btn btn-outline-primary btn-sm">{{ __('Voir tous mes badges') }}</a>

                    @else
                        <div class="py-3">
                            <i class="fas fa-award fa-3x text-gray-300 mb-3"></i>
                            <p>{{ __('Commencez à lire pour débloquer des badges !') }}</p>
                            <a href="{{ route('reader.badges') }}" class="btn btn-outline-primary btn-sm">{{ __('Voir les badges') }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
