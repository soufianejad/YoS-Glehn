@extends('layouts.public')

@section('title', 'Bibliothèque - ' . config('platform.name'))

@section('content')

<div class="container py-4">
    
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-3"><i class="bi bi-book"></i> {{ __('Bibliothèque Publique') }}</h1>
            <p class="lead text-muted">{{ __('Explorez notre collection de littérature africaine') }}</p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('library.index') }}" method="GET" class="row g-3">
                        
                        <!-- Search -->
                        <div class="col-md-4">
                            <label class="form-label">{{ __('Rechercher') }}</label>
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="{{ __('Titre, auteur...') }}" value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="col-md-3">
                            <label class="form-label">{{ __('Catégorie') }}</label>
                            <select name="category" class="form-select">
                                <option value="">{{ __('Toutes les catégories') }}</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Language Filter -->
                        <div class="col-md-2">
                            <label class="form-label">{{ __('Langue') }}</label>
                            <select name="language" class="form-select">
                                <option value="">{{ __('Toutes') }}</option>
                                @foreach(config('platform.languages') as $code => $name)
                                    <option value="{{ $code }}" {{ request('language') == $code ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Type Filter -->
                        <div class="col-md-2">
                            <label class="form-label">{{ __('Type') }}</label>
                            <select name="type" class="form-select">
                                <option value="">{{ __('Tous') }}</option>
                                <option value="pdf" {{ request('type') == 'pdf' ? 'selected' : '' }}>{{ __('PDF uniquement') }}</option>
                                <option value="audio" {{ request('type') == 'audio' ? 'selected' : '' }}>{{ __('Audio uniquement') }}</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-funnel"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('library.index') }}" class="btn btn-sm {{ !request()->has('category') ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="bi bi-grid"></i> {{ __('Tous') }}
                </a>
                @foreach($categories->take(5) as $cat)
                    <a href="{{ route('library.category', $cat->slug) }}" class="btn btn-sm btn-outline-primary">
                        {{ $cat->name }}
                    </a>
                @endforeach
                <a href="{{ route('library.popular') }}" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-fire"></i> {{ __('Populaires') }}
                </a>
                <a href="{{ route('library.recent') }}" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-clock"></i> {{ __('Nouveautés') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Results Count -->
    <div class="row mb-3">
        <div class="col-md-12">
            <p class="text-muted">
                <strong>{{ $books->total() }}</strong> livre(s) trouvé(s)
            </p>
        </div>
    </div>

    <!-- Books Grid -->
    <div class="row">
        @forelse($books as $book)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 shadow-sm book-card">
                    
                    <!-- Cover Image -->
                    <div class="position-relative">
                        <img src="{{ $book->cover_url }}" class="card-img-top" alt="{{ $book->title }}" style="height: 300px; object-fit: cover;">
                        
                        <!-- Featured Badge -->
                        @if($book->is_featured)
                            <span class="position-absolute top-0 start-0 m-2 badge bg-warning">
                                <i class="bi bi-star-fill"></i> {{ __('Coup de cœur') }}
                            </span>
                        @endif

                        <!-- Format Badges -->
                        <div class="position-absolute bottom-0 end-0 m-2 d-flex gap-1">
                            @if($book->hasPdf())
                                <span class="badge bg-danger"><i class="bi bi-file-pdf"></i></span>
                            @endif
                            @if($book->hasAudio())
                                <span class="badge bg-primary"><i class="bi bi-headphones"></i></span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body d-flex flex-column">
                        
                        <!-- Title -->
                        <h5 class="card-title text-truncate" title="{{ $book->title }}">
                            {{ $book->title }}
                        </h5>
                        
                        <!-- Author -->
                        <p class="text-muted small mb-2">
                            <i class="bi bi-person"></i> {{ $book->author->full_name }}
                        </p>

                        <!-- Category -->
                        <div class="mb-2">
                            <span class="badge bg-primary">{{ $book->category->name }}</span>
                            <span class="badge bg-secondary">{{ config('platform.languages')[$book->language] ?? $book->language }}</span>
                        </div>

                        <!-- Rating -->
                        <div class="d-flex align-items-center mb-2">
                            <div class="text-warning me-2">
                                @for($i = 0; $i < 5; $i++)
                                    @if($i < floor($book->average_rating))
                                        <i class="bi bi-star-fill"></i>
                                    @elseif($i < ceil($book->average_rating))
                                        <i class="bi bi-star-half"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                            </div>
                            <small class="text-muted">({{ $book->reviews_count }})</small>
                        </div>

                        <!-- Stats -->
                        <div class="small text-muted mb-3">
                            <i class="bi bi-eye"></i> {{ number_format($book->views_count) }} vues
                            <i class="bi bi-book ms-2"></i> {{ number_format($book->reads_count) }} lectures
                        </div>

                        <!-- Price or Action Button -->
                        <div class="mt-auto">
                            @auth
                                @if(auth()->user()->hasAccessToBook($book))
                                    <a href="{{ route('book.show', $book->slug) }}" class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-book-half"></i> {{ __('Lire maintenant') }}
                                    </a>
                                @else
                                    <a href="{{ route('book.show', $book->slug) }}" class="btn btn-outline-primary btn-sm w-100">
                                        {{ __('Voir les détails') }}
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('book.show', $book->slug) }}" class="btn btn-outline-primary btn-sm w-100">
                                    {{ __('Voir les détails') }}
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle fs-1 d-block mb-3"></i>
                    <h4>{{ __('Aucun livre trouvé') }}</h4>
                    <p class="mb-3">{{ __('Essayez de modifier vos critères de recherche') }}</p>
                    <a href="{{ route('library.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise"></i> {{ __('Réinitialiser les filtres') }}
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="row mt-4">
        <div class="col-md-12">
            {{ $books->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
.book-card {
    transition: all 0.3s ease;
}

.book-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

.card-img-top {
    transition: opacity 0.3s ease;
}

.book-card:hover .card-img-top {
    opacity: 0.9;
}
</style>
@endpush