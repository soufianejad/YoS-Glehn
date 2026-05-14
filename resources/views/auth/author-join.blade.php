@extends('layouts.app')

@section('content')
<div class="author-join-page">
    <!-- Hero Section -->
    <section class="hero-section bg-primary text-white py-5">
        <div class="container py-lg-5">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">{{ __('Devenez Auteur et Partagez votre Passion') }}</h1>
                    <p class="lead mb-4">{{ __('Rejoignez notre communauté d\'auteurs talentueux et commencez à publier vos ouvrages dès aujourd\'hui. Touchez des milliers de lecteurs à travers le monde.') }}</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('author.register') }}" class="btn btn-light btn-lg">{{ __('Commencer maintenant') }}</a>
                        <a href="#benefits" class="btn btn-outline-light btn-lg">{{ __('En savoir plus') }}</a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <img src="{{ asset('images/author-hero.png') }}" alt="Author Illustration" class="img-fluid rounded-3 shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold">{{ __('Pourquoi nous rejoindre ?') }}</h2>
                <p class="text-muted">{{ __('Découvrez les avantages de publier sur notre plateforme') }}</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-4 text-center">
                        <div class="icon-wrapper mb-3">
                            <i class="bi bi-graph-up text-primary fs-1"></i>
                        </div>
                        <h4 class="fw-bold">{{ __('Visibilité Maximale') }}</h4>
                        <p class="text-muted">{{ __('Mettez vos œuvres en avant auprès d\'une audience ciblée et passionnée par la lecture.') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-4 text-center">
                        <div class="icon-wrapper mb-3">
                            <i class="bi bi-cash-stack text-success fs-1"></i>
                        </div>
                        <h4 class="fw-bold">{{ __('Revenus Justes') }}</h4>
                        <p class="text-muted">{{ __('Bénéficiez d\'un système de rémunération transparent et avantageux sur chaque vente.') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm p-4 text-center">
                        <div class="icon-wrapper mb-3">
                            <i class="bi bi-tools text-warning fs-1"></i>
                        </div>
                        <h4 class="fw-bold">{{ __('Outils d\'Auteur') }}</h4>
                        <p class="text-muted">{{ __('Accédez à un tableau de bord complet pour gérer vos publications et suivre vos statistiques.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section class="bg-light py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold">{{ __('Comment ça marche ?') }}</h2>
                <p class="text-muted">{{ __('Trois étapes simples pour commencer votre aventure') }}</p>
            </div>
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="step-item d-flex mb-4">
                        <div class="step-number me-4 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0;">1</div>
                        <div>
                            <h5 class="fw-bold">{{ __('Créez votre compte') }}</h5>
                            <p class="text-muted">{{ __('Inscrivez-vous en quelques clics et complétez votre profil d\'auteur.') }}</p>
                        </div>
                    </div>
                    <div class="step-item d-flex mb-4">
                        <div class="step-number me-4 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0;">2</div>
                        <div>
                            <h5 class="fw-bold">{{ __('Publiez vos livres') }}</h5>
                            <p class="text-muted">{{ __('Téléchargez vos manuscrits, ajoutez des couvertures et définissez vos prix.') }}</p>
                        </div>
                    </div>
                    <div class="step-item d-flex">
                        <div class="step-number me-4 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0;">3</div>
                        <div>
                            <h5 class="fw-bold">{{ __('Suivez vos ventes') }}</h5>
                            <p class="text-muted">{{ __('Consultez vos rapports de ventes et recevez vos paiements en toute sécurité.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <img src="{{ asset('images/how-it-works.png') }}" alt="Steps Illustration" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5">
        <div class="container py-5 text-center">
            <h2 class="fw-bold mb-4">{{ __('Prêt à faire entendre votre voix ?') }}</h2>
            <p class="lead mb-4 mx-auto" style="max-width: 700px;">{{ __('Rejoignez des centaines d\'auteurs qui nous font déjà confiance pour la diffusion de leurs œuvres.') }}</p>
            <a href="{{ route('author.register') }}" class="btn btn-primary btn-lg px-5">{{ __('Créer mon compte auteur') }}</a>
        </div>
    </section>
</div>

<style>
    .hero-section {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }
    .icon-wrapper i {
        font-size: 3rem;
    }
    .step-number {
        font-size: 1.25rem;
        font-weight: bold;
    }
    .card:hover {
        transform: translateY(-5px);
        transition: transform 0.3s ease;
    }
</style>
@endsection
