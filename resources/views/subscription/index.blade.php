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

                <!-- Avantages du plan -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">{{ __('Vos Avantages Inclus') }}</h5>
                        <div class="row g-4">
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="feature-icon text-primary"><i class="fas fa-file-pdf"></i></div>
                                <div>
                                    <div class="fw-bold">{{ __('Accès PDF') }}</div>
                                    <div class="small text-muted">{{ $subscription->subscriptionPlan->pdf_access ? __('Lecture illimitée') : __('Non inclus') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="feature-icon text-success"><i class="fas fa-headphones"></i></div>
                                <div>
                                    <div class="fw-bold">{{ __('Accès Audio') }}</div>
                                    <div class="small text-muted">{{ $subscription->subscriptionPlan->audio_access ? __('Écoute illimitée') : __('Non inclus') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="feature-icon text-warning"><i class="fas fa-download"></i></div>
                                <div>
                                    <div class="fw-bold">{{ __('Téléchargement') }}</div>
                                    <div class="small text-muted">{{ $subscription->subscriptionPlan->download_access ? __('Remises exclusives') : __('Non inclus') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="feature-icon text-info"><i class="fas fa-question-circle"></i></div>
                                <div>
                                    <div class="fw-bold">{{ __('Accès Quiz') }}</div>
                                    <div class="small text-muted">{{ $subscription->subscriptionPlan->quiz_access ? __('Certification incluse') : __('Non inclus') }}</div>
                                </div>
                            </div>
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

        <!-- Historique -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-0">{{ __('Historique des Abonnements') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 small text-uppercase text-muted">{{ __('Plan') }}</th>
                                    <th class="py-3 small text-uppercase text-muted">{{ __('Fin') }}</th>
                                    <th class="pe-4 py-3 text-end small text-uppercase text-muted">{{ __('Statut') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $sub)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold">{{ $sub->subscriptionPlan->name }}</div>
                                            <div class="small text-muted">{{ number_format($sub->subscriptionPlan->price, 0) }} XOF</div>
                                        </td>
                                        <td>
                                            <div class="small">{{ $sub->end_date->format('d/m/Y') }}</div>
                                        </td>
                                        <td class="pe-4 text-end">
                                            @php
                                                $statusClass = match($sub->status) {
                                                    'active' => 'bg-success',
                                                    'pending' => 'bg-warning text-dark',
                                                    'cancelled' => 'bg-secondary',
                                                    'expired' => 'bg-danger',
                                                    default => 'bg-info'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }} rounded-pill">{{ __($sub->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-muted">
                                            {{ __('Aucun historique disponible.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($history->hasPages())
                        <div class="p-4 border-top">
                            {{ $history->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
