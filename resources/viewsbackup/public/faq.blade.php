@extends('layouts.app')

@section('title', __('Questions Fréquemment Posées') . ' - ' . config('platform.name'))

@push('styles')
<style>
    .faq-header {
        padding: 5rem 0;
        background-color: #f8f9fa;
        text-align: center;
    }
    .accordion-button:not(.collapsed) {
        color: #fff;
        background-color: var(--primary-color);
        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .125);
    }
    .accordion-button:not(.collapsed)::after {
        filter: brightness(0) invert(1);
    }
</style>
@endpush

@section('content')
<div class="faq-header">
    <div class="container">
        <h1 class="display-4 font-weight-bold">{{ __('Questions Fréquemment Posées (FAQ)') }}</h1>
        <p class="lead text-muted">{{ __('Trouvez rapidement les réponses à vos questions.') }}</p>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="accordion shadow-sm" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <i class="fas fa-question-circle me-2"></i> {{ __('Comment s\'inscrire sur la plateforme ?') }}
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>{{ __('faq.how_to_register_answer') }}</p>
                            <p>Pour vous inscrire, cliquez sur le bouton "S'inscrire" en haut à droite de la page et remplissez le formulaire. Vous pouvez choisir entre un compte lecteur, étudiant (avec code d'accès) ou auteur.</p>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            <i class="fas fa-book-reader me-2"></i> {{ __('Comment puis-je lire ou écouter des livres ?') }}
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>{{ __('faq.how_to_read_answer') }}</p>
                            <p>Après vous être abonné(e) ou avoir acheté un livre, vous pouvez y accéder depuis votre bibliothèque personnelle. Cliquez sur le livre pour commencer à lire le PDF ou écouter la version audio directement sur la plateforme.</p>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <i class="fas fa-mobile-alt me-2"></i> {{ __('Puis-je lire sur mon téléphone ou ma tablette ?') }}
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>{{ __('faq.can_i_read_on_mobile_answer') }}</p>
                            <p>Oui, notre plateforme est entièrement responsive et accessible depuis n'importe quel appareil mobile ou tablette. Profitez de vos lectures où que vous soyez !</p>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            <i class="fas fa-dollar-sign me-2"></i> {{ __('Comment fonctionnent les abonnements et les achats ?') }}
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Nous proposons des plans d'abonnement pour un accès illimité à une vaste sélection de livres. Vous pouvez également acheter des livres à l'unité. Les écoles bénéficient de plans spécifiques avec des fonctionnalités de gestion.</p>
                        </div>
                    </div>
                </div>
                 <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            <i class="fas fa-headset me-2"></i> {{ __('Comment contacter le support client ?') }}
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>{{ __('faq.how_to_contact_support_answer') }}</p>
                            <p>Vous pouvez nous contacter via le formulaire sur notre page <a href="{{ route('contact') }}">Contact</a>. Notre équipe de support est disponible pour répondre à toutes vos questions.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action Section -->
<section class="bg-primary text-white text-center py-5 mt-5">
    <div class="container py-4">
        <h2 class="display-4 font-weight-bold mb-4">{{ __('Votre question n\'est pas listée ici ?') }}</h2>
        <p class="lead mb-5">{{ __('N\'hésitez pas à nous contacter directement pour toute assistance supplémentaire.') }}</p>
        <a href="{{ route('contact') }}" class="btn btn-light btn-lg text-primary font-weight-bold">{{ __('Nous Contacter') }}</a>
    </div>
</section>
@endsection