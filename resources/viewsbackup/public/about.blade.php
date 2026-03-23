@extends('layouts.app')

@section('title', __('À Propos de Nous') . ' - ' . config('platform.name'))

@push('styles')
<style>
    .about-header {
        padding: 5rem 0;
        background-color: #f8f9fa;
        text-align: center;
    }
    .value-card, .team-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .value-card:hover, .team-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15)!important;
    }
    .team-member-img {
        width: 150px;
        height: 150px;
        object-fit: cover;
    }
</style>
@endpush

@section('content')
<div class="about-header">
    <div class="container">
        <h1 class="display-4 font-weight-bold">{{ __('Notre Histoire') }}</h1>
        <p class="lead text-muted">{{ __('Découvrez qui nous sommes et ce qui nous motive.') }}</p>
    </div>
</div>

<div class="container py-5">
    <!-- Our Mission -->
    <section class="mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow border-0 p-4 p-md-5">
                    <h2 class="text-center mb-4 text-primary font-weight-bold">{{ __('Notre Mission') }}</h2>
                    <p class="lead text-center text-gray-700 mb-4">{{ __('yosglehn.com a été créé avec une vision claire :') }}</p>
                    <p class="text-center text-gray-800">
                        {{ __('Rendre la richesse de la littérature africaine accessible au monde entier, tout en encourageant la lecture et l\'apprentissage à travers une plateforme intuitive et moderne.') }}
                        {{ __('Nous croyons au pouvoir des histoires pour éduquer, inspirer et connecter les cultures.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Values -->
    <section class="py-5">
        <h2 class="text-center mb-5 text-primary font-weight-bold">{{ __('Nos Valeurs') }}</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card value-card h-100 text-center p-4 shadow-sm border-0">
                    <div class="card-body">
                        <i class="fas fa-heart fa-3x text-primary mb-3"></i>
                        <h5 class="card-title font-weight-bold">{{ __('Passion') }}</h5>
                        <p class="card-text text-muted">{{ __('Nous sommes passionnés par la promotion de la lecture et la mise en valeur des voix africaines.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card value-card h-100 text-center p-4 shadow-sm border-0">
                    <div class="card-body">
                        <i class="fas fa-globe fa-3x text-success mb-3"></i>
                        <h5 class="card-title font-weight-bold">{{ __('Accessibilité') }}</h5>
                        <p class="card-text text-muted">{{ __('Rendre le savoir et les histoires accessibles à tous, partout dans le monde.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card value-card h-100 text-center p-4 shadow-sm border-0">
                    <div class="card-body">
                        <i class="fas fa-lightbulb fa-3x text-info mb-3"></i>
                        <h5 class="card-title font-weight-bold">{{ __('Innovation') }}</h5>
                        <p class="card-text text-muted">{{ __('Utiliser la technologie pour créer des expériences de lecture et d\'apprentissage novatrices.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Team -->
    <section class="py-5 bg-light">
        <h2 class="text-center mb-5 text-primary font-weight-bold">{{ __('Notre Équipe') }}</h2>
        <div class="row justify-content-center">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card team-card h-100 text-center p-3 shadow-sm border-0">
                    <img src="https://i.pravatar.cc/150?img=6" class="rounded-circle mx-auto mb-3 team-member-img" alt="Membre de l'équipe">
                    <h5 class="font-weight-bold mb-1">{{ __('Jane Doe') }}</h5>
                    <p class="text-muted small">{{ __('Fondatrice & CEO') }}</p>
                    <p class="small text-gray-700">"{{ __('Engagée à bâtir une communauté de lecteurs passionnés.') }}"</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card team-card h-100 text-center p-3 shadow-sm border-0">
                    <img src="https://i.pravatar.cc/150?img=8" class="rounded-circle mx-auto mb-3 team-member-img" alt="Membre de l'équipe">
                    <h5 class="font-weight-bold mb-1">{{ __('John Smith') }}</h5>
                    <p class="text-muted small">{{ __('Directeur Technique') }}</p>
                    <p class="small text-gray-700">"{{ __('Innover pour une expérience utilisateur sans faille.') }}"</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card team-card h-100 text-center p-3 shadow-sm border-0">
                    <img src="https://i.pravatar.cc/150?img=12" class="rounded-circle mx-auto mb-3 team-member-img" alt="Membre de l'équipe">
                    <h5 class="font-weight-bold mb-1">{{ __('Emily White') }}</h5>
                    <p class="text-muted small">{{ __('Responsable Contenu') }}</p>
                    <p class="small text-gray-700">"{{ __('Curatrice des plus belles histoires africaines.') }}"</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
