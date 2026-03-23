@extends('layouts.dashboard')

@section('title', 'Tableau de Bord')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Bienvenue, {{ Auth::user()->first_name }}!</h1>
        <a href="{{ route('adult.library.index') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-book-reader fa-sm text-white-50 me-2"></i>
            Explorer la Bibliothèque Adulte
        </a>
    </div>

    <!-- Recently Accessed Books -->
    <div class="mb-5">
        <h2 class="h5 mb-4 text-gray-800">Continuer la lecture</h2>
        @if($recentlyAccessedBooks->isEmpty())
            <div class="alert alert-light">
                Vous n'avez commencé aucun livre de cette section.
            </div>
        @else
            <div class="row">
                @foreach($recentlyAccessedBooks as $book)
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm lift-hover">
                            <div class="row g-0">
                                <div class="col-4">
                                    <img src="{{ $book->cover_image_url }}" class="img-fluid rounded-start" alt="{{ $book->title }}" style="height: 100%; object-fit: cover;">
                                </div>
                                <div class="col-8">
                                    <div class="card-body d-flex flex-column h-100">
                                        <h6 class="card-title text-truncate" title="{{ $book->title }}">{{ $book->title }}</h6>
                                        <p class="card-text small text-muted flex-grow-1">par {{ $book->author->name ?? 'N/A' }}</p>
                                        <a href="{{ $book->audio_file ? route('adult.library.listen', $book->slug) : route('adult.library.read', $book->slug) }}" class="btn btn-primary btn-sm mt-auto stretched-link">
                                            Reprendre
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Newest Additions -->
    <div>
        <h2 class="h5 mb-4 text-gray-800">Nouveautés</h2>
        @if($newAdultBooks->isEmpty())
            <div class="alert alert-light">
                Aucun nouveau livre pour le moment.
            </div>
        @else
            <div class="row">
                @foreach($newAdultBooks as $book)
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm lift-hover">
                            <img src="{{ $book->cover_image_url }}" class="card-img-top" alt="{{ $book->title }}" style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-truncate" title="{{ $book->title }}">{{ $book->title }}</h5>
                                <p class="card-text small text-muted flex-grow-1">par {{ $book->author->name ?? 'N/A' }}</p>
                                <a href="{{ route('adult.library.show', $book->slug) }}" class="btn btn-outline-primary btn-sm mt-auto stretched-link">Découvrir</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
