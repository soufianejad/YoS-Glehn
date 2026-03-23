@extends('layouts.app')

@section('title', __('Accueil - Une Odyssée Littéraire Africaine') . ' - ' . config('platform.name'))

@push('styles')
<style>
    .hero {
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80') no-repeat center center;
        background-size: cover;
        min-height: 60vh;
        color: white;
    }
    .bg-light-gray {
        background-color: #f8f9fa;
    }
    .author-avatar {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #dee2e6;
    }
    .book-card .card-img-top {
        height: 300px;
        object-fit: cover;
    }
    .star-rating {
        color: #ffc107;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">

    <!-- Hero Section -->
    <section class="hero text-center d-flex align-items-center justify-content-center">
        <div class="hero-content">
            <h1 class="display-3 font-weight-bold animate__animated animate__fadeInDown">{{ __("Découvrez les Trésors de la Littérature Africaine") }}</h1>
            <p class="lead my-4 animate__animated animate__fadeInUp">{{ __('Votre portail exclusif pour des histoires qui inspirent, éduquent et transportent.') }}</p>
            <div class="hero-cta animate__animated animate__zoomIn">
                <a href="{{ route('library.index') }}" class="btn btn-primary btn-lg">{{ __('Explorer la Bibliothèque') }}</a>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">{{ __("S'inscrire Maintenant") }}</a>
                @endguest
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <h2 class="section-title text-center mb-4">{{ __('Trouvez Votre Prochaine Lecture') }}</h2>
                <div class="search-bar-container shadow-sm">
                    <form action="{{ route('library.search') }}" method="GET" class="form-inline justify-content-center">
                        <div class="input-group input-group-lg w-100">
                            <input type="text" class="form-control" name="search" placeholder="{{ __('Rechercher par titre, auteur ou mot-clé...') }}" aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> {{__('Rechercher')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="bg-light-gray py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="stat-item">
                        <i class="bi bi-book-half display-4 text-primary mb-3"></i>
                        <h3 class="stat-number font-weight-bold">{{ $stats['books'] }}+</h3>
                        <p class="stat-label text-muted">{{ __('Livres Disponibles') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <i class="bi bi-people-fill display-4 text-primary mb-3"></i>
                        <h3 class="stat-number font-weight-bold">{{ $stats['authors'] }}+</h3>
                        <p class="stat-label text-muted">{{ __('Auteurs Talentueux') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <i class="bi bi-person-check-fill display-4 text-primary mb-3"></i>
                        <h3 class="stat-number font-weight-bold">{{ $stats['users'] }}+</h3>
                        <p class="stat-label text-muted">{{ __('Lecteurs Passionnés') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Books Section -->
    <section class="container py-5">
        <h2 class="section-title text-center mb-5">{{ __('Notre Sélection à la Une') }}</h2>
        <div class="row">
            @forelse($featuredBooks as $book)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card book-card h-100 shadow-sm border-0">
                        <img src="{{ $book->cover_url ?? 'https://via.placeholder.com/300x400' }}" class="card-img-top" alt="{{ $book->title }}">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ Str::limit($book->title, 50) }}</h5>
                            <p class="card-text text-muted mb-2">{{ $book->author->name }}</p>
                            <div class="star-rating mb-3">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $book->average_rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                                <span class="text-muted small">({{ $book->reviews_count }})</span>
                            </div>
                            <p class="card-text small">{{ Str::limit($book->description, 100) }}</p>
                            <a href="{{ route('book.show', $book->slug) }}" class="btn btn-primary mt-auto">{{ __('Voir Détails') }}</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted">{{__('Aucun livre à la une pour le moment.')}}</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Author Spotlight Section -->
    @if($featuredAuthors->isNotEmpty())
    <section class="bg-light-gray py-5">
        <div class="container">
            <h2 class="section-title text-center mb-5">{{ __('Auteurs à Découvrir') }}</h2>
            <div class="row text-center">
                @foreach($featuredAuthors as $author)
                    <div class="col-lg-3 col-md-6 mb-4">
                        <img src="{{ $author->avatar_url }}" class="author-avatar mb-3" alt="{{ $author->name }}">
                        <h5 class="font-weight-bold">{{ $author->name }}</h5>
                        <a href="{{ route('public.author.show', $author) }}" class="btn btn-outline-primary btn-sm mt-2">{{ __('Voir le profil') }}</a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- How It Works Section -->
    <section class="container py-5">
        <h2 class="section-title text-center mb-5">{{ __('Comment ça Marche ?') }}</h2>
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="card h-100 p-4 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-person-plus-fill display-4 text-secondary mb-3"></i>
                        <h5 class="card-title font-weight-bold">{{ __('1. Créez un Compte') }}</h5>
                        <p class="card-text">{{ __('Inscription simple et rapide pour accéder à des milliers de titres.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 p-4 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-compass display-4 text-accent mb-3"></i>
                        <h5 class="card-title font-weight-bold">{{ __('2. Découvrez des Livres') }}</h5>
                        <p class="card-text">{{ __('Parcourez notre bibliothèque, trouvez des auteurs et genres variés.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 p-4 border-0 shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-book-half display-4 text-primary mb-3"></i>
                        <h5 class="card-title font-weight-bold">{{ __('3. Lisez et Apprenez') }}</h5>
                        <p class="card-text">{{ __("Lisez ou écoutez vos livres favoris sur n'importe quel appareil.") }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="bg-light-gray py-5">
        <div class="container">
            <h2 class="section-title text-center mb-5">{{ __('Ce que disent nos lecteurs') }}</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card testimonial-card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://i.pravatar.cc/60?img=1" class="rounded-circle" alt="Avatar">
                                <div class="ml-3">
                                    <h6 class="mb-0 font-weight-bold">Aïssatou Diallo</h6>
                                    <div class="star-rating">
                                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="card-text font-italic">"{{ __("Une plateforme incroyable ! J'ai découvert tellement de nouveaux auteurs africains. C'est une vraie mine d'or.") }}"</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card testimonial-card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="https://i.pravatar.cc/60?img=2" class="rounded-circle" alt="Avatar">
                                <div class="ml-3">
                                    <h6 class="mb-0 font-weight-bold">Koffi Annan</h6>
                                     <div class="star-rating">
                                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="card-text font-italic">"{{ __("Simple à utiliser et une collection de livres impressionnante. Le mode lecture est très confortable. Je recommande vivement.") }}"</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="bg-primary text-white text-center py-5">
        <div class="container py-4">
            <h2 class="display-4 font-weight-bold mb-4 animate__animated animate__fadeInUp">{{ __('Prêt à Commencer Votre Aventure ?') }}</h2>
            <p class="lead mb-5 animate__animated animate__fadeInUp animate__delay-1s">{{ __("Rejoignez notre communauté et plongez dans l'univers de la lecture africaine dès aujourd'hui.") }}</p>
            @guest
                <a href="{{ route('register') }}" class="btn btn-light btn-lg text-primary font-weight-bold animate__animated animate__zoomIn animate__delay-2s">{{ __("Je m'inscris !") }}</a>
            @else
                <a href="{{ route('library.index') }}" class="btn btn-light btn-lg text-primary font-weight-bold animate__animated animate__zoomIn animate__delay-2s">{{ __('Accéder à la bibliothèque') }}</a>
            @endguest
        </div>
    </section>

</div>
@endsection