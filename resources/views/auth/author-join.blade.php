@extends('layouts.app')

@section('title', __('Devenir Auteur') . ' - ' . config('platform.name'))

@push('styles')
<style>
    .hero {
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1455390582262-044cdead277a?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80') no-repeat center center;
        background-size: cover;
        min-height: 40vh;
        color: white;
    }
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

@section('content')
<div class="container-fluid p-0">
    <!-- Hero Section -->
    <section class="hero text-center d-flex align-items-center justify-content-center">
        <div class="hero-content">
            <h1 class="display-3 font-weight-bold">{{ __('Devenez Auteur') }}</h1>
            <p class="lead my-4">{{ __("Partagez votre passion, touchez des milliers de lecteurs et vivez de votre plume.") }}</p>
            <div>
                <a href="{{ route('author.register') }}" class="btn btn-primary btn-lg px-4 fw-bold shadow-sm">{{ __('Commencer mon aventure') }}</a>
            </div>
        </div>
    </section>
</div>

<div class="container py-5 bg-light">
    <!-- Introduction Section -->
    <div class="row mb-5 text-center bg-white p-4 rounded shadow-sm mx-0">
        <div class="col-md-10 mx-auto">
            <h2 class="fw-bold mb-3">{{ __('Rejoignez notre communauté d\'auteurs talentueux') }}</h2>
            <p class="lead text-muted">{{ __('Nous offrons une plateforme intuitive et performante pour vous aider à publier, promouvoir et vendre vos ouvrages en toute simplicité.') }}</p>
        </div>
    </div>

    <!-- Benefits Section -->
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 p-4 text-center section-card bg-white">
                <div class="mb-3">
                    <i class="bi bi-graph-up text-primary fs-1"></i>
                </div>
                <h4 class="fw-bold">{{ __('Visibilité Maximale') }}</h4>
                <p class="text-muted mb-0">{{ __('Mettez vos œuvres en avant auprès d\'une audience ciblée et passionnée par la lecture africaine.') }}</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 p-4 text-center section-card bg-white">
                <div class="mb-3">
                    <i class="bi bi-cash-stack text-success fs-1"></i>
                </div>
                <h4 class="fw-bold">{{ __('Revenus Justes') }}</h4>
                <p class="text-muted mb-0">{{ __('Bénéficiez d\'un système de rémunération transparent et avantageux sur chaque vente de vos livres.') }}</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 p-4 text-center section-card bg-white">
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
            <div class="card shadow-sm border-0 p-4 p-md-5 bg-white">
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
    <div class="row mt-5">
        <div class="col-md-12 text-center py-5 bg-white rounded shadow-sm">
            <h3 class="fw-bold mb-4">{{ __('Prêt à faire entendre votre voix ?') }}</h3>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('author.register') }}" class="btn btn-primary btn-lg px-5 shadow-sm">{{ __('Créer mon compte auteur') }}</a>
                <a href="#how-it-works" class="btn btn-outline-secondary btn-lg px-5">{{ __('En savoir plus') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
