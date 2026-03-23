@extends('layouts.adult')

@section('title', 'Bibliothèque Adulte')
@section('header', 'Bibliothèque Adulte')

@push('styles')
<style>
    .book-card .card-img-top {
        height: 250px;
        object-fit: cover;
    }
    .empty-state {
        text-align: center;
        padding: 4rem;
        border: 2px dashed #e3e6f0;
        border-radius: 0.75rem;
    }
    .empty-state i {
        font-size: 4rem;
        color: #e3e6f0;
    }
    .filter-card {
        border-radius: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Filter and Search Form -->
    <div class="card shadow-sm mb-4 filter-card">
        <div class="card-body">
            <form action="{{ route('adult.library.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label font-weight-bold">Rechercher par Titre</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="e.g., Le Rouge et le Noir">
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label font-weight-bold">Catégorie</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Toutes</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="reading_status" class="form-label font-weight-bold">Statut</label>
                    <select class="form-select" id="reading_status" name="reading_status">
                        <option value="">Tous</option>
                        <option value="not_started" {{ request('reading_status') == 'not_started' ? 'selected' : '' }}>Non commencé</option>
                        <option value="in_progress" {{ request('reading_status') == 'in_progress' ? 'selected' : '' }}>En cours</option>
                        <option value="finished" {{ request('reading_status') == 'finished' ? 'selected' : '' }}>Terminé</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    @if($books->isEmpty())
        <div class="empty-state bg-white shadow-sm mt-5">
            <i class="fas fa-book-dead mb-4"></i>
            <h3 class="text-gray-800">Aucun livre ne correspond à votre recherche</h3>
            <p class="text-muted">Essayez de modifier vos filtres ou explorez toutes les catégories.</p>
            <a href="{{ route('adult.library.index') }}" class="btn btn-primary mt-3">
                Réinitialiser les filtres
            </a>
        </div>
    @else
        <div class="row">
            @foreach($books as $book)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card book-card h-100 shadow-sm border-0 lift-hover">
                        <a href="{{ route('adult.library.show', $book->slug) }}">
                            <img src="{{ $book->cover_image_url }}" class="card-img-top" alt="{{ $book->title }}">
                        </a>
                        <div class="card-body d-flex flex-column p-3">
                            <h6 class="card-title font-weight-bold mb-1"><a href="{{ route('adult.library.show', $book->slug) }}" class="text-gray-900 stretched-link">{{ Str::limit($book->title, 40) }}</a></h6>
                            @if($book->author)
                                <p class="small text-muted mb-2">{{ $book->author->name }}</p>
                            @endif

                            @php
                                $progress = $book->readingProgress->first();
                            @endphp

                            @if($progress && $progress->progress_percentage > 0)
                                <div class="progress mt-auto" style="height: 5px;" title="{{ round($progress->progress_percentage) }}% terminé">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $progress->progress_percentage }}%;" aria-valuenow="{{ $progress->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $books->links() }}
        </div>
    @endif
</div>
@endsection
