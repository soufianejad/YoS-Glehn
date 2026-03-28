@extends('layouts.dashboard')

@section('title', __('Mon Abonnement'))

@push('styles')
<style>
    .sub-card {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        border: none;
        border-radius: 1.5rem;
        color: white;
        overflow: hidden;
        position: relative;
    }
    .sub-card::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
    }
    .feature-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row g-4">
        <!-- Carte Abonnement Actuel -->
        <div class="col-lg-7">
            @if($subscription)
                <div class="card sub-card shadow-lg mb-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h4 class="mb-1 text-white opacity-75">{{ __('Plan Actuel') }}</h4>
                                <h2 class="display-6 fw-bold text-white mb-0">{{ $subscription->subscriptionPlan->name }}</h2>
                            </div>
                            <span class="status-badge bg-white text-primary shadow-sm">
                                <i class="fas fa-check-circle me-1"></i> {{ strtoupper($subscription->status) }}
                            </span>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-4">
                                <div class="small opacity-75 mb-1">{{ __('Date de début') }}</div>
                                <div class="fw-bold h5 mb-0">{{ $subscription->start_date->format('d M Y') }}</div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="small opacity-75 mb-1">{{ __('Prochaine échéance') }}</div>
                                <div class="fw-bold h5 mb-0">{{ $subscription->end_date->format('d M Y') }}</div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="small opacity-75 mb-1">{{ __('Temps restant') }}</div>
                                <div class="fw-bold h5 mb-0">{{ $subscription->daysRemaining() }} {{ __('jours') }}</div>
                            </div>
                        </div>

                        <div class="progress bg-white bg-opacity-25 mb-4" style="height: 10px; border-radius: 5px;">
                            @php
                                $totalDays = $subscription->start_date->diffInDays($subscription->end_date);
                                $usedDays = $subscription->start_date->diffInDays(now());
                                $percent = $totalDays > 0 ? min(100, max(0, ($usedDays / $totalDays) * 100)) : 0;
                            @endphp
                            <div class="progress-bar bg-white" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            @if($subscription->auto_renew)
                                <form action="{{ route('subscription.cancel') }}" method="POST" onsubmit="return confirm('{{ __('Voulez-vous vraiment annuler le renouvellement automatique ?') }}')">
                                    @csrf
                                    <button type="submit" class="btn btn-light text-primary fw-bold rounded-pill px-4">
                                        <i class="fas fa-times-circle me-2"></i>{{ __('Désactiver le renouvellement') }}
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('subscription.checkout.renew') }}" class="btn btn-white fw-bold rounded-pill px-4" style="background: white; color: #4f46e5;">
                                    <i class="fas fa-redo me-2"></i>{{ __('Réactiver / Renouveler') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm rounded-4 text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-ghost fa-4x text-light"></i>
                    </div>
                    <h3 class="fw-bold">{{ __('Aucun abonnement actif') }}</h3>
                    <p class="text-muted mb-4">{{ __('Abonnez-vous à l\'un de nos plans pour débloquer l\'accès complet à notre bibliothèque de livres.') }}</p>
                    <a href="{{ route('subscription.plans') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
                        {{ __('Découvrir les Plans') }}
                    </a>
                </div>
            @endif
        </div>

        <!-- Plans Disponibles -->
        <div class="col-lg-12 mt-5">
            <h3 class="fw-bold text-center mb-4">{{ __('Découvrez nos autres plans') }}</h3>
            <div class="row justify-content-center">
                @forelse($plans as $plan)
                    <div class="col-lg-4 mb-5">
                        <div class="card pricing-card h-100 shadow-sm {{ !empty($plan->is_popular) ? 'popular' : '' }} {{ ($currentSubscription && $currentSubscription->subscription_plan_id == $plan->id) ? 'border-success' : '' }}">
                            @if($currentSubscription && $currentSubscription->subscription_plan_id == $plan->id)
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge bg-success">{{ __('Plan actuel') }}</span>
                                </div>
                            @endif
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
                                    <li>{{ __('Accès à toute la bibliothèque') }}</li>
                                    <li><i class="fas {{ $plan->pdf_access ? 'fa-check-circle' : 'fa-times-circle text-muted' }}"></i> {{__("Accès aux PDFs")}}</li>
                                    <li><i class="fas {{ $plan->audio_access ? 'fa-check-circle' : 'fa-times-circle text-muted' }}"></i> {{__("Accès aux Audios")}}</li>
                                    <li><i class="fas {{ $plan->download_access ? 'fa-check-circle' : 'fa-times-circle text-muted' }}"></i> {{__("Téléchargement")}}</li>
                                    <li><i class="fas {{ $plan->quiz_access ? 'fa-check-circle' : 'fa-times-circle text-muted' }}"></i> {{__("Accès aux Quiz")}}</li>
                                     @if($plan->max_students)
                                        <li><i class="fas fa-users"></i> {{__("Jusqu'à")}} {{ $plan->max_students }} {{__("étudiants")}}</li>
                                    @endif
                                </ul>
                            </div>
                            <div class="card-footer text-center">
                                @if($currentSubscription && $currentSubscription->subscription_plan_id == $plan->id)
                                    <button class="btn btn-success w-100" disabled><i class="fas fa-check-circle me-1"></i> {{ __("Plan Actuel") }}</button>
                                @else
                                    <a href="{{ route('subscription.checkout', $plan) }}" class="btn btn-primary w-100">{{ __("Choisir ce Plan") }}</a>
                                @endif
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
    </div>
</div>
@endsection
