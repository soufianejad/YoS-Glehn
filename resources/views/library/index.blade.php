@extends('layouts.app')

@section('title', isset($category) ? $category->name : __('Bibliothèque') . ' - ' . config('platform.name'))

@push('styles')
<style>
    .hero {
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80') no-repeat center center;
        background-size: cover;
        min-height: 40vh;
        color: white;
    }
    .book-card .card-img-top {
        height: 300px;
        object-fit: cover;
    }
    .star-rating {
        color: #ffc107;
    }
    .sidebar-widget {
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0.5rem;
    }
    .category-list .list-group-item {
        border: none;
        padding: 0.75rem 0;
        background-color: transparent;
    }
    .category-list .list-group-item a {
        text-decoration: none;
        color: #212529;
        transition: color 0.2s;
    }
    .category-list .list-group-item a:hover,
    .category-list .list-group-item.active a {
        color: var(--primary-color);
        font-weight: bold;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <!-- Hero Section -->
    <section class="hero text-center d-flex align-items-center justify-content-center">
        <div class="hero-content">
            <h1 class="display-3 font-weight-bold animate__animated animate__fadeInDown">{{ __('Notre Bibliothèque') }}</h1>
            <p class="lead my-4 animate__animated animate__fadeInUp">{{ __('Plongez dans un monde de savoir et d\'aventure.') }}</p>
        </div>
    </section>
</div>

<div class="container py-5 bg-light">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="sidebar-widget">
                <h4 class="mb-4">{{ __('Filtres') }}</h4>
                <form action="{{ route('library.index') }}" method="GET">
                    <!-- Search -->
                    <div class="mb-4">
                        <label for="search" class="form-label font-weight-bold">{{ __('Recherche') }}</label>
                        <input type="text" name="search" id="search" class="form-control" value="{{ $search ?? '' }}" placeholder="Titre, auteur...">
                    </div>

                    <!-- Category -->
                    <div class="mb-4">
                        <label for="category" class="form-label font-weight-bold">{{ __('Catégorie') }}</label>
                        <select name="category" id="category" class="form-select">
                            <option value="">{{ __('Toutes') }}</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->slug }}" @if(isset($category) && $cat->id === $category->id) selected @endif>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                     <!-- Type -->
                    <div class="mb-4">
                        <label class="form-label font-weight-bold">{{ __('Format') }}</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type_all" value="" {{ !$typeFilter ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_all">{{ __('Tous') }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type_pdf" value="pdf" {{ $typeFilter === 'pdf' ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_pdf">{{ __('Livre (PDF)') }}</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="type_audio" value="audio" {{ $typeFilter === 'audio' ? 'checked' : '' }}>
                            <label class="form-check-label" for="type_audio">{{ __('Livre Audio') }}</label>
                        </div>
                    </div>

                    <!-- Language -->
                    <div class="mb-4">
                        <label for="language" class="form-label font-weight-bold">{{ __('Langue') }}</label>
                        <select name="language" id="language" class="form-select">
                            <option value="">{{ __('Toutes') }}</option>
                            <option value="fr" {{ $languageFilter === 'fr' ? 'selected' : '' }}>{{ __('Français') }}</option>
                            <option value="en" {{ $languageFilter === 'en' ? 'selected' : '' }}>{{ __('Anglais') }}</option>
                            {{-- Add other languages from a config or db --}}
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">{{ __('Filtrer') }}</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Main content -->
        <div class="col-lg-9 bg-white p-4 rounded">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    @if(isset($category))
                        {{ $category->name }}
                    @elseif($search)
                        {{ __('Résultats pour') }} "{{ $search }}"
                    @else
                        {{ __('Tous les livres') }}
                    @endif
                </h2>
                <span class="text-muted">{{ $books->total() }} {{ __('livres trouvés') }}</span>
            </div>

            <div class="row">
                @forelse($books as $book)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card book-card h-100 shadow-sm border-0">
                             <a href="{{ route('book.show', $book->slug) }}">
                                <img src="{{ $book->cover_image_url }}" class="card-img-top" alt="{{ $book->title }}">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><a href="{{ route('book.show', $book->slug) }}" class="text-dark">{{ Str::limit($book->title, 50) }}</a></h5>
                                <p class="card-text text-muted mb-2">{{ $book->author->name }}</p>
                                <div class="star-rating mb-3">
                                    @php $rating = $book->reviews->avg('rating'); @endphp
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $rating ? 'text-warning' : 'text-gray-300' }}"></i>
                                    @endfor
                                    <span class="text-muted small">({{ $book->reviews->count() }})</span>
                                </div>
                                <div class="mt-auto">
                                    <a href="{{ route('book.show', $book->slug) }}" class="btn btn-primary w-100">{{ __('Voir Détails') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                             <i class="fas fa-search-minus fa-4x text-gray-300 mb-3"></i>
                            <h4>{{ __('Aucun livre ne correspond à vos critères') }}</h4>
                            <p class="text-muted">{{ __('Essayez d\'ajuster vos filtres ou de faire une nouvelle recherche.') }}</p>
                            <a href="{{ route('library.index') }}" class="btn btn-primary mt-2">{{ __('Réinitialiser les filtres') }}</a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $books->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection