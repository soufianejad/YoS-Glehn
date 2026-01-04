@extends('layouts.app')

@section('title', __('Nos Plans d\'Abonnement') . ' - ' . config('platform.name'))

@push('styles')
<style>
    .hero {
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1770&q=80') no-repeat center center;
        background-size: cover;
        min-height: 40vh;
        color: white;
    }
    .pricing-card {
        border: 1px solid #e3e6f0;
        transition: all 0.3s ease;
    }
    .pricing-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,0.175)!important;
    }
    .pricing-card.popular {
        border-width: 3px;
        border-color: var(--primary-color) !important;
    }
    .pricing-card .card-header {
        background-color: transparent;
        border-bottom: none;
    }
    .pricing-card .card-title {
        font-size: 1.5rem;
        font-weight: 700;
    }
    .pricing-card .price {
        font-size: 3.5rem;
        font-weight: 700;
    }
    .pricing-card .price .currency {
        font-size: 1.5rem;
        font-weight: 400;
        vertical-align: super;
    }
    .feature-list {
        padding-left: 0;
        list-style: none;
    }
    .feature-list li {
        margin-bottom: 1rem;
    }
    .feature-list i {
        color: var(--primary-color);
        width: 20px;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    <!-- Hero Section -->
    <section class="hero text-center d-flex align-items-center justify-content-center">
        <div class="hero-content">
            <h1 class="display-3 font-weight-bold animate__animated animate__fadeInDown">{{ __('Nos Plans d\'Abonnement') }}</h1>
            <p class="lead my-4 animate__animated animate__fadeInUp">{{ __('Choisissez le plan qui vous convient et débloquez un monde de connaissances.') }}</p>
        </div>
    </section>
</div>

<section class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            @forelse($plans as $plan)
                <div class="col-lg-4 mb-5">
                    <div class="card pricing-card h-100 shadow-sm {{ !empty($plan->is_popular) ? 'popular' : '' }}">
                        @if(!empty($plan->is_popular))
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-primary">{{ __('Populaire') }}</span>
                            </div>
                        @endif
                        <div class="card-header text-center py-4">
                            <h4 class="card-title">{{ $plan->name }}</h4>
                            <p class="text-muted">{{ $plan->description }}</p>
                        </div>
                        <div class="card-body text-center">
                            <div class="price mb-4">
                                <span class="currency">F</span>{{ number_format($plan->price, 0) }}
                                <small class="text-muted">/ {{ $plan->duration_days }} jours</small>
                            </div>
                            <ul class="feature-list text-left">
                                <li><i class="fas fa-check-circle"></i> Accès à toute la bibliothèque</li>
                                <li><i class="fas {{ $plan->pdf_access ? 'fa-check-circle' : 'fa-times-circle text-muted' }}"></i> Accès aux PDFs</li>
                                <li><i class="fas {{ $plan->audio_access ? 'fa-check-circle' : 'fa-times-circle text-muted' }}"></i> Accès aux Audios</li>
                                <li><i class="fas {{ $plan->download_access ? 'fa-check-circle' : 'fa-times-circle text-muted' }}"></i> Téléchargement</li>
                                <li><i class="fas {{ $plan->quiz_access ? 'fa-check-circle' : 'fa-times-circle text-muted' }}"></i> Accès aux Quiz</li>
                                 @if($plan->max_students)
                                    <li><i class="fas fa-users"></i> Jusqu'à {{ $plan->max_students }} étudiants</li>
                                @endif
                            </ul>
                        </div>
                         <div class="card-footer text-center">
                            <form action="{{ route('subscription.subscribe', $plan) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">{{ __("Choisir ce Plan") }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">{{ __("Aucun plan d'abonnement disponible pour le moment.") }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="mb-4">{{ __('Questions Fréquemment Posées') }}</h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Puis-je annuler mon abonnement à tout moment ?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Oui, vous pouvez annuler votre abonnement à tout moment depuis votre tableau de bord. Vous conserverez l'accès jusqu'à la fin de votre période de facturation en cours.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Quels sont les moyens de paiement acceptés ?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Nous acceptons les principales cartes de crédit (Visa, MasterCard) ainsi que les paiements mobiles pour certaines régions.
                            </div>
                        </div>
                    </div>
                     <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                               Y a-t-il une différence entre les plans pour écoles et les plans individuels ?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Oui, les plans pour écoles sont conçus pour plusieurs utilisateurs (étudiants et enseignants) et incluent des outils de gestion de classe. Les plans individuels sont pour un usage personnel.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection