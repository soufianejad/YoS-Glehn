@extends('layouts.dashboard')

@section('title', __('Mon Abonnement'))

@push('styles')
<style>
    :root {
        --sub-primary: #4f46e5;
        --sub-secondary: #7c3aed;
        --sub-success: #10b981;
        --sub-warning: #f59e0b;
        --sub-danger: #ef4444;
        --sub-light: #f8fafc;
    }

    .sub-hero-card {
        background: linear-gradient(135deg, var(--sub-primary) 0%, var(--sub-secondary) 100%);
        border: none;
        border-radius: 1.5rem;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
    }

    .sub-hero-card::before {
        content: '';
        position: absolute;
        top: -100px;
        right: -100px;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        z-index: 0;
    }

    .sub-hero-card .card-body {
        position: relative;
        z-index: 1;
    }

    .status-badge {
        padding: 0.6rem 1.2rem;
        border-radius: 2rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        font-size: 0.7rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .plan-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
        border-radius: 1.25rem;
    }

    .plan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .plan-card.active-plan {
        border-color: var(--sub-primary);
        background-color: rgba(79, 70, 229, 0.02);
    }

    .feature-list li {
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        color: #475569;
    }

    .feature-list i {
        color: var(--sub-success);
        margin-right: 12px;
        font-size: 1.1rem;
    }

    .history-table thead th {
        background-color: var(--sub-light);
        border: none;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        padding: 1rem;
    }

    .history-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    .btn-action {
        border-radius: 0.75rem;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
    }

    .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    .price-tag {
        font-size: 2.5rem;
        font-weight: 800;
        color: #1e293b;
    }

    .price-period {
        color: #64748b;
        font-size: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">{{ __('Mon Abonnement') }}</h1>
            <p class="text-muted mb-0">{{ __('Gérez votre abonnement et découvrez nos plans.') }}</p>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Tableau de bord') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Abonnement') }}</li>
            </ol>
        </nav>
    </div>

    <div class="row g-4">
        <!-- Current Subscription & Quick Stats -->
        <div class="col-xl-8">
            @if($subscription)
                <div class="card sub-hero-card mb-4">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-4">
                            <div class="mb-3 mb-md-0">
                                <span class="badge bg-opacity-20 text-white mb-2 px-3 py-2 rounded-pill">{{ strtoupper(__('Plan actuel')) }}</span>
                                <h2 class="display-5 fw-bold text-white mb-0">{{ $subscription->subscriptionPlan->name }}</h2>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                @php
                                    $statusClass = match($subscription->status) {
                                        'active' => 'bg-white text-success',
                                        'cancelled' => 'bg-white text-warning',
                                        'expired' => 'bg-white text-danger',
                                        default => 'bg-white text-primary'
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    <i class="fas fa-circle me-1 small"></i> {{ strtoupper(__($subscription->status == 'active' ? 'Active' : ($subscription->status == 'cancelled' ? 'Cancelled' : ucfirst($subscription->status)))) }}
                                </span>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-6 col-md-3">
                                <div class="opacity-75 small mb-1">{{ __('Date de début') }}</div>
                                <div class="fw-bold h5 text-white mb-0">{{ $subscription->start_date->format('d/m/Y') }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="opacity-75 small mb-1">{{ __('Date de fin') }}</div>
                                <div class="fw-bold h5 text-white mb-0">{{ $subscription->end_date->format('d/m/Y') }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="opacity-75 small mb-1">{{ __('Prix') }}</div>
                                <div class="fw-bold h5 text-white mb-0">{{ number_format($subscription->subscriptionPlan->price, 0) }} XOF</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="opacity-75 small mb-1">{{ __('Statut') }}</div>
                                <div class="fw-bold h5 text-white mb-0">{{ number_format($subscription->daysRemaining(), 0) }} {{ __('jours') }}</div>
                            </div>
                        </div>

                        @php
                            $totalDays = $subscription->start_date->diffInDays($subscription->end_date);
                            $usedDays = $subscription->start_date->diffInDays(now());
                            $percent = $totalDays > 0 ? min(100, max(0, ($usedDays / $totalDays) * 100)) : 0;
                        @endphp
                        <div class="progress bg-white bg-opacity-20 mb-4" style="height: 12px; border-radius: 6px;">
                            <div class="progress-bar bg-white shadow-sm" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <div class="d-flex flex-wrap gap-3">
                            @if($subscription->status === 'active')
                                <a href="{{ route('subscription.checkout.renew') }}" class="btn btn-light text-primary btn-action shadow-sm">
                                    <i class="fas fa-sync-alt me-2"></i>{{ __('Renouveler') }}
                                </a>
                                @if($subscription->auto_renew)
                                    <form action="{{ route('subscription.cancel') }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to cancel your subscription?') }}')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-light btn-action">
                                            <i class="fas fa-pause-circle me-2"></i>{{ __('Annuler l\'abonnement') }}
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('subscription.plans') }}" class="btn btn-light text-primary btn-action shadow-sm">
                                    <i class="fas fa-rocket me-2"></i>{{ __('Découvrir les Plans') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-4 mb-4">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary mx-auto mb-4" style="width: 80px; height: 80px; font-size: 2.5rem;">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h2 class="fw-bold text-dark">{{ __('No Active Subscription') }}</h2>
                    <p class="text-muted mx-auto mb-4" style="max-width: 500px;">
                        {{ __('It looks like you do not have an active subscription. Subscribe today to get unlimited access to our library!') }}
                    </p>
                    <a href="#plans-section" class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow">
                        {{ __('Browse Plans') }}
                    </a>
                </div>
            @endif

            <!-- Benefits / Features Section -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold text-dark mb-4">{{ __('Vos Avantages') }}</h4>
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3 text-center text-md-start">
                            <div class="icon-box bg-info bg-opacity-10 text-info mx-auto mx-md-0">
                                <i class="fas fa-book-reader"></i>
                            </div>
                            <h6 class="fw-bold mb-1">{{ __('Accès aux PDFs') }}</h6>
                            <p class="small text-muted mb-0">{{ __('Lecture illimitée') }}</p>
                        </div>
                        <div class="col-md-6 col-lg-3 text-center text-md-start">
                            <div class="icon-box bg-success bg-opacity-10 text-success mx-auto mx-md-0">
                                <i class="fas fa-headphones-alt"></i>
                            </div>
                            <h6 class="fw-bold mb-1">{{ __('Accès aux Audios') }}</h6>
                            <p class="small text-muted mb-0">{{ __('Écoute illimitée') }}</p>
                        </div>
                        <div class="col-md-6 col-lg-3 text-center text-md-start">
                            <div class="icon-box bg-warning bg-opacity-10 text-warning mx-auto mx-md-0">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <h6 class="fw-bold mb-1">{{ __('Accès aux Quiz') }}</h6>
                            <p class="small text-muted mb-0">{{ __('Certification') }}</p>
                        </div>
                        <div class="col-md-6 col-lg-3 text-center text-md-start">
                            <div class="icon-box bg-danger bg-opacity-10 text-danger mx-auto mx-md-0">
                                <i class="fas fa-cloud-download-alt"></i>
                            </div>
                            <h6 class="fw-bold mb-1">{{ __('Téléchargement') }}</h6>
                            <p class="small text-muted mb-0">{{ __('Accès') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: History -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">{{ __('Subscription History') }}</h5>
                    <i class="fas fa-history text-muted"></i>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table history-table hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Plan') }} / {{ __('Date') }}</th>
                                    <th class="text-end">{{ __('Statut') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $sub)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $sub->subscriptionPlan->name }}</div>
                                            <div class="small text-muted">{{ $sub->created_at->format('d M Y') }} • {{ number_format($sub->subscriptionPlan->price, 0) }} XOF</div>
                                        </td>
                                        <td class="text-end">
                                            @php
                                                $statusClass = match($sub->status) {
                                                    'active' => 'bg-success text-white',
                                                    'pending' => 'bg-warning text-dark',
                                                    'cancelled' => 'bg-secondary text-white',
                                                    'expired' => 'bg-danger text-white',
                                                    default => 'bg-info text-white'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }} rounded-pill px-2 py-1" style="font-size: 0.65rem;">
                                                {{ strtoupper(__($sub->status == 'active' ? 'Active' : ($sub->status == 'cancelled' ? 'Cancelled' : ucfirst($sub->status)))) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox d-block mb-3 fa-2x opacity-20"></i>
                                            {{ __('No past subscriptions found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($history->hasPages())
                    <div class="card-footer bg-transparent border-0 p-4">
                        {{ $history->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Plans Section -->
    <div id="plans-section" class="mt-5 pt-4">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-dark">{{ __('Nos Plans d\'Abonnement') }}</h2>
            <p class="text-muted">{{ __('Choisissez le plan qui vous convient et débloquez un monde de connaissances.') }}</p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($plans as $plan)
                <div class="col-md-6 col-lg-4">
                    <div class="card plan-card h-100 shadow-sm border-0 {{ $subscription && $subscription->subscription_plan_id === $plan->id ? 'active-plan' : '' }}">
                        <div class="card-body p-4 p-xl-5 d-flex flex-column">
                            @if($subscription && $subscription->subscription_plan_id === $plan->id)
                                <div class="text-center mb-3">
                                    <span class="badge bg-primary px-3 py-2 rounded-pill">{{ strtoupper(__('Plan actuel')) }}</span>
                                </div>
                            @endif

                            <div class="text-center mb-4">
                                <span class="badge {{ $plan->type === 'school' ? 'bg-info' : 'bg-secondary' }} mb-2 rounded-pill opacity-75">
                                    {{ $plan->type === 'school' ? __('École') : __('Individual') }}
                                </span>
                                <h4 class="fw-bold text-dark mb-3">{{ $plan->name }}</h4>
                                <div class="price-tag">
                                    {{ number_format($plan->price, 0) }}<span class="h6 fw-normal text-muted ms-1">XOF</span>
                                </div>
                                <div class="price-period">{{ $plan->duration_days }} {{ __('jours') }}</div>
                            </div>

                            <hr class="my-4 opacity-10">

                            <ul class="list-unstyled feature-list mb-5 flex-grow-1">
                                @if($plan->description)
                                    <li class="mb-4 text-center d-block">
                                        <span class="text-muted italic">"{{ $plan->description }}"</span>
                                    </li>
                                @endif
                                
                                <li>
                                    <i class="fas fa-check-circle"></i>
                                    <span>{{ __('Accès aux PDFs') }}</span>
                                </li>
                                <li>
                                    <i class="fas fa-check-circle"></i>
                                    <span>{{ __('Accès aux Audios') }}</span>
                                </li>
                                <li>
                                    <i class="fas fa-check-circle"></i>
                                    <span>{{ __('Accès aux Quiz') }}</span>
                                </li>
                                <li>
                                    <i class="fas fa-check-circle"></i>
                                    <span>{{ __('Accès à toute la bibliothèque') }}</span>
                                </li>
                            </ul>

                            <div class="d-grid">
                                @if($subscription && $subscription->subscription_plan_id === $plan->id)
                                    <button class="btn btn-outline-primary btn-action" disabled>
                                        {{ __('Plan Actuel') }}
                                    </button>
                                @else
                                    <a href="{{ route('subscription.checkout', $plan) }}" class="btn btn-primary btn-action shadow-sm">
                                        {{ __('Choisir ce Plan') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>
@endpush
