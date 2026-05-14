@extends('layouts.public')

@section('title', __('Devenir Auteur') . ' - ' . config('platform.name'))

@section('content')
<div class="container py-4">

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="mb-3"><i class="bi bi-pencil-square text-primary"></i> {{ __('Devenez Auteur et Partagez votre Passion') }}</h1>
            <p class="lead text-muted">{{ __('Rejoignez notre communauté d\'auteurs talentueux et commencez à publier vos ouvrages dès aujourd\'hui.') }}</p>
        </div>
    </div>

    <!-- Hero / CTA Section -->
    <div class="row mb-5">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, #0d6efd 0%, #003d99 100%);">
                <div class="card-body p-5 text-white">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <h2 class="display-5 fw-bold mb-4">{{ __('Prêt à faire entendre votre voix ?') }}</h2>
                            <p class="lead mb-4 opacity-75">{{ __('Touchez des milliers de lecteurs à travers le monde avec notre plateforme de publication intuitive et performante.') }}</p>
                            <div class="d-flex gap-3">
                                <a href="{{ route('author.register') }}" class="btn btn-light btn-lg px-4 fw-bold text-primary">{{ __('Créer mon compte auteur') }}</a>
                                <a href="#how-it-works" class="btn btn-outline-light btn-lg px-4">{{ __('Comment ça marche ?') }}</a>
                            </div>
                        </div>
                        <div class="col-lg-4 d-none d-lg-block text-center">
                            <i class="bi bi-book-half" style="font-size: 10rem; opacity: 0.2;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Benefits Section -->
    <div class="row mb-5">
        <div class="col-md-12 mb-4 text-center">
            <h2 class="fw-bold">{{ __('Pourquoi nous rejoindre ?') }}</h2>
            <p class="text-muted">{{ __('Découvrez les avantages de publier sur notre plateforme') }}</p>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 p-4 text-center section-card">
                <div class="mb-3">
                    <i class="bi bi-graph-up text-primary fs-1"></i>
                </div>
                <h4 class="fw-bold">{{ __('Visibilité Maximale') }}</h4>
                <p class="text-muted mb-0">{{ __('Mettez vos œuvres en avant auprès d\'une audience ciblée et passionnée par la lecture africaine.') }}</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 p-4 text-center section-card">
                <div class="mb-3">
                    <i class="bi bi-cash-stack text-success fs-1"></i>
                </div>
                <h4 class="fw-bold">{{ __('Revenus Justes') }}</h4>
                <p class="text-muted mb-0">{{ __('Bénéficiez d\'un système de rémunération transparent et avantageux sur chaque vente de vos livres.') }}</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 p-4 text-center section-card">
                <div class="mb-3">
                    <i class="bi bi-tools text-warning fs-1"></i>
                </div>
                <h4 class="fw-bold">{{ __('Outils d\'Auteur') }}</h4>
                <p class="text-muted mb-0">{{ __('Accédez à un tableau de bord complet pour gérer vos publications et suivre vos statistiques en temps réel.') }}</p>
            </div>
        </div>
    </div>

    <!-- How it Works Section -->
    <div id="how-it-works" class="row mb-5 pt-4">
        <div class="col-md-12 mb-4 text-center">
            <h2 class="fw-bold">{{ __('Comment ça marche ?') }}</h2>
            <p class="text-muted">{{ __('Trois étapes simples pour commencer votre aventure') }}</p>
        </div>
        <div class="col-md-12">
            <div class="card shadow-sm border-0 p-4 p-md-5">
                <div class="row g-4 text-center text-md-start">
                    <div class="col-md-4">
                        <div class="d-flex flex-column flex-md-row align-items-center gap-3">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0; font-size: 1.5rem; font-weight: bold;">1</div>
                            <div>
                                <h5 class="fw-bold mb-1">{{ __('Créez votre compte') }}</h5>
                                <p class="text-muted small mb-0">{{ __('Inscrivez-vous en quelques clics et complétez votre profil d\'auteur professionnel.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex flex-column flex-md-row align-items-center gap-3 border-md-start ps-md-4">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0; font-size: 1.5rem; font-weight: bold;">2</div>
                            <div>
                                <h5 class="fw-bold mb-1">{{ __('Publiez vos livres') }}</h5>
                                <p class="text-muted small mb-0">{{ __('Téléchargez vos manuscrits, ajoutez des couvertures et définissez vos prix librement.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex flex-column flex-md-row align-items-center gap-3 border-md-start ps-md-4">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0; font-size: 1.5rem; font-weight: bold;">3</div>
                            <div>
                                <h5 class="fw-bold mb-1">{{ __('Suivez vos ventes') }}</h5>
                                <p class="text-muted small mb-0">{{ __('Consultez vos rapports et recevez vos paiements en toute sécurité sur votre compte.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Final CTA -->
    <div class="row">
        <div class="col-md-12 text-center py-4">
            <h3 class="fw-bold mb-4">{{ __('Rejoignez des centaines d\'auteurs qui nous font déjà confiance.') }}</h3>
            <a href="{{ route('author.register') }}" class="btn btn-primary btn-lg px-5 shadow-sm">{{ __('Commencer mon aventure') }}</a>
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
.section-card {
    transition: all 0.3s ease;
}

.section-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}

@media (min-width: 768px) {
    .border-md-start {
        border-left: 1px solid #dee2e6!important;
    }
}
</style>
@endpush
