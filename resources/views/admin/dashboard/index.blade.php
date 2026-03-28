@extends('layouts.dashboard')

@section('title', __('Tableau de Bord Administrateur'))
@section('header', __('Tableau de Bord Administrateur'))

@push('styles')
<style>
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 0.5rem;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
    }
    .stat-card .stat-icon-wrap {
        width: 3rem;
        height: 3rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .quick-link-card {
        transition: all 0.2s ease-in-out;
        border-radius: 0.5rem;
    }
    .quick-link-card:hover {
        background-color: #f8f9fc;
        transform: translateY(-2px);
    }
    .admin-section-title {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #6c757d;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    .rank-badge {
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .mix-bar {
        height: 0.5rem;
        border-radius: 0.25rem;
        overflow: hidden;
        display: flex;
        width: 100%;
        background: #e9ecef;
    }
    .mix-bar > span {
        display: block;
        height: 100%;
        flex-shrink: 0;
        min-width: 0;
        transition: width 0.3s ease;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">{{ __('Vue d\'ensemble de la plateforme') }}</h1>
            <p class="text-muted small mb-0">{{ __('KPI financiers, auteurs, livres et actions prioritaires.') }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap mt-2 mt-sm-0">
            <a href="{{ route('admin.statistics') }}" class="btn btn-sm btn-outline-secondary shadow-sm">
                <i class="fas fa-chart-line me-1"></i> {{ __('Statistiques') }}
            </a>
            <a href="{{ route('admin.settings.index') }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-cogs fa-sm"></i> {{ __('Paramètres') }}
            </a>
        </div>
    </div>

    {{-- Ligne 1 : revenus + utilisateurs --}}
    <p class="admin-section-title mb-2">{{ __('Finances & croissance') }}</p>
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow h-100 stat-card border-start border-primary border-4">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">{{ __('Revenus (mois)') }}</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($monthlyRevenue, 0, ',', ' ') }} F</div>
                        <small class="text-muted">{{ __('dont abonnements :') }} {{ number_format($subscriptionRevenueMonth, 0, ',', ' ') }} F</small>
                    </div>
                    <div class="stat-icon-wrap bg-primary bg-opacity-10 text-primary"><i class="fas fa-calendar-day fa-lg"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow h-100 stat-card border-start border-success border-4">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">{{ __('Revenus (année)') }}</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($annualRevenue, 0, ',', ' ') }} F</div>
                        <small class="text-muted">{{ __('Total historique :') }} {{ number_format($totalRevenue, 0, ',', ' ') }} F</small>
                    </div>
                    <div class="stat-icon-wrap bg-success bg-opacity-10 text-success"><i class="fas fa-coins fa-lg"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow h-100 stat-card border-start border-info border-4">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">{{ __('Nouveaux inscrits (mois)') }}</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $newUsersThisMonth }}</div>
                        <small class="text-muted">{{ __('Utilisateurs total :') }} {{ $totalUsers }}</small>
                    </div>
                    <div class="stat-icon-wrap bg-info bg-opacity-10 text-info"><i class="fas fa-user-plus fa-lg"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow h-100 stat-card border-start border-warning border-4">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">{{ __('Transactions (mois)') }}</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $paymentsCountMonth }}</div>
                        <small class="text-muted">{{ __('Panier moyen :') }} {{ number_format($avgOrderValueMonth, 0, ',', ' ') }} F</small>
                    </div>
                    <div class="stat-icon-wrap bg-warning bg-opacity-10 text-warning"><i class="fas fa-receipt fa-lg"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ligne 2 : catalogue & abonnements --}}
    <p class="admin-section-title mb-2">{{ __('Catalogue & abonnements') }}</p>
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow h-100 stat-card border-start border-secondary border-4">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs fw-bold text-secondary text-uppercase mb-1">{{ __('Livres publiés') }}</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $publishedBooks }}</div>
                        <small class="text-muted">{{ __('Tous statuts :') }} {{ $totalBooks }}</small>
                    </div>
                    <div class="stat-icon-wrap bg-secondary bg-opacity-10 text-secondary"><i class="fas fa-book fa-lg"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow h-100 stat-card border-start border-dark border-4">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs fw-bold text-dark text-uppercase mb-1">{{ __('Auteurs') }}</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalAuthors }}</div>
                    </div>
                    <div class="stat-icon-wrap bg-dark bg-opacity-10"><i class="fas fa-pen-fancy fa-lg"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow h-100 stat-card border-start border-danger border-4">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs fw-bold text-danger text-uppercase mb-1">{{ __('Abonnements actifs') }}</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $activeSubscriptions }}</div>
                    </div>
                    <div class="stat-icon-wrap bg-danger bg-opacity-10 text-danger"><i class="fas fa-id-card fa-lg"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow h-100 stat-card border-start border-primary border-4">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">{{ __('Écoles') }}</div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalSchools }}</div>
                    </div>
                    <div class="stat-icon-wrap bg-primary bg-opacity-10 text-primary"><i class="fas fa-school fa-lg"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mix revenus du mois --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <h6 class="mb-1 fw-bold text-gray-800">{{ __('Répartition des revenus (mois en cours)') }}</h6>
                            <p class="small text-muted mb-0">{{ __('Basée sur les paiements complétés (brut encaissé).') }}</p>
                        </div>
                        <div class="col-md-8">
                            <div class="mix-bar mb-2" role="img" aria-label="{{ __('Répartition abonnements / ventes directes') }}">
                                @if($subscriptionShareMonth > 0)
                                    <span style="width: {{ $subscriptionShareMonth }}%; background: #4e73df;" title="{{ __('Abonnements') }}"></span>
                                @endif
                                @if($directShareMonth > 0)
                                    <span style="width: {{ $directShareMonth }}%; background: #1cc88a;" title="{{ __('Ventes directes (PDF / audio)') }}"></span>
                                @endif
                            </div>
                            <div class="d-flex flex-wrap gap-3 small">
                                <span><span class="d-inline-block rounded-circle me-1" style="width:0.6rem;height:0.6rem;background:#4e73df;"></span> {{ __('Abonnements') }} <strong>{{ $subscriptionShareMonth }}%</strong> · {{ number_format($subscriptionRevenueMonth, 0, ',', ' ') }} F</span>
                                <span><span class="d-inline-block rounded-circle me-1" style="width:0.6rem;height:0.6rem;background:#1cc88a;"></span> {{ __('Ventes directes') }} <strong>{{ $directShareMonth }}%</strong> · {{ number_format($directSalesRevenueMonth, 0, ',', ' ') }} F</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top auteurs & top livres --}}
    <p class="admin-section-title mb-2">{{ __('Classements (revenus enregistrés dans « Revenus »)') }}</p>
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-trophy text-warning me-2"></i>{{ __('Top auteurs (volume généré)') }}</h6>
                    <a href="{{ route('admin.revenues.authors') }}" class="btn btn-sm btn-outline-primary">{{ __('Tout voir') }}</a>
                </div>
                <div class="card-body p-0">
                    @forelse($topAuthors as $index => $row)
                        <div class="d-flex align-items-center px-3 py-3 border-bottom @if($loop->last) border-bottom-0 @endif">
                            <span class="rank-badge me-3 {{ $index === 0 ? 'bg-warning text-dark' : 'bg-light text-muted' }}">{{ $index + 1 }}</span>
                            <div class="flex-grow-1 min-width-0">
                                @if($row->author)
                                    <a href="{{ route('admin.revenues.author-detail', $row->author) }}" class="fw-bold text-gray-800 text-decoration-none d-block text-truncate">{{ $row->author->name }}</a>
                                    <small class="text-muted">{{ __('Part auteur cumulée :') }} {{ number_format($row->author_earnings, 0, ',', ' ') }} F · {{ $row->allocations }} {{ __('lignes revenus') }}</small>
                                @else
                                    <span class="text-muted">{{ __('Auteur inconnu') }}</span>
                                @endif
                            </div>
                            <div class="text-end ms-2">
                                <div class="fw-bold text-gray-800">{{ number_format($row->volume, 0, ',', ' ') }} F</div>
                                <small class="text-muted text-uppercase">{{ __('volume') }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted small py-5 mb-0">{{ __('Aucun revenu enregistré pour l’instant.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-book-open text-success me-2"></i>{{ __('Top livres (montants attribués)') }}</h6>
                    <a href="{{ route('admin.books.index') }}" class="btn btn-sm btn-outline-primary">{{ __('Livres') }}</a>
                </div>
                <div class="card-body p-0">
                    @forelse($topBooks as $index => $book)
                        <div class="d-flex align-items-center px-3 py-3 border-bottom @if($loop->last) border-bottom-0 @endif">
                            <span class="rank-badge me-3 {{ $index === 0 ? 'bg-success text-white' : 'bg-light text-muted' }}">{{ $index + 1 }}</span>
                            <div class="flex-grow-1 min-width-0">
                                <a href="{{ route('admin.books.show', $book->id) }}" class="fw-bold text-gray-800 text-decoration-none d-block text-truncate">{{ $book->title }}</a>
                                <small class="text-muted">{{ trim($book->author_first.' '.$book->author_last) }}</small>
                            </div>
                            <div class="text-end ms-2">
                                <div class="fw-bold text-gray-800">{{ number_format($book->volume, 0, ',', ' ') }} F</div>
                                <small class="text-muted">{{ $book->allocations }} {{ __('attributions') }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted small py-5 mb-0">{{ __('Aucun revenu rattaché à un livre.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 fw-bold text-primary">{{ __('Aperçu des revenus (12 mois)') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 320px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-white py-3 border-bottom"><h6 class="m-0 fw-bold text-primary">{{ __('Accès rapides') }}</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-gray-800 quick-link-card d-block p-3 border rounded h-100 text-center">
                                <i class="fas fa-users-cog fa-2x text-primary mb-2"></i>
                                <h6 class="mb-0">{{ __('Utilisateurs') }}</h6>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <a href="{{ route('admin.books.index') }}" class="text-decoration-none text-gray-800 quick-link-card d-block p-3 border rounded h-100 text-center">
                                <i class="fas fa-book-open fa-2x text-success mb-2"></i>
                                <h6 class="mb-0">{{ __('Livres') }}</h6>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <a href="{{ route('admin.schools.index') }}" class="text-decoration-none text-gray-800 quick-link-card d-block p-3 border rounded h-100 text-center">
                                <i class="fas fa-school fa-2x text-info mb-2"></i>
                                <h6 class="mb-0">{{ __('Écoles') }}</h6>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <a href="{{ route('admin.payments.index') }}" class="text-decoration-none text-gray-800 quick-link-card d-block p-3 border rounded h-100 text-center">
                                <i class="fas fa-credit-card fa-2x text-danger mb-2"></i>
                                <h6 class="mb-0">{{ __('Paiements') }}</h6>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <a href="{{ route('admin.revenues.index') }}" class="text-decoration-none text-gray-800 quick-link-card d-block p-3 border rounded h-100 text-center">
                                <i class="fas fa-funnel-dollar fa-2x text-warning mb-2"></i>
                                <h6 class="mb-0">{{ __('Revenus') }}</h6>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <a href="{{ route('admin.settings.index') }}" class="text-decoration-none text-gray-800 quick-link-card d-block p-3 border rounded h-100 text-center">
                                <i class="fas fa-cogs fa-2x text-secondary mb-2"></i>
                                <h6 class="mb-0">{{ __('Paramètres') }}</h6>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow mb-4 border-start border-danger border-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 fw-bold text-danger"><i class="fas fa-exclamation-circle me-1"></i>{{ __('Actions requises') }}</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.books.pending') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-book fa-fw me-2 text-secondary"></i>{{ __('Livres en attente') }}</span>
                        <span class="badge bg-danger rounded-pill">{{ $pendingBooks }}</span>
                    </a>
                    <a href="{{ route('admin.reviews.pending') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-star-half-alt fa-fw me-2 text-secondary"></i>{{ __('Avis en attente') }}</span>
                        <span class="badge bg-danger rounded-pill">{{ $pendingReviews }}</span>
                    </a>
                    <a href="{{ route('admin.revenues.payouts.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-hand-holding-usd fa-fw me-2 text-secondary"></i>{{ __('Versements en attente') }}</span>
                        <span class="badge bg-danger rounded-pill">{{ $pendingPayouts }}</span>
                    </a>
                </div>
                @if($pendingRevenueAmount > 0)
                    <div class="card-body bg-light border-top small text-muted">
                        {{ __('Revenus à valider (montants bruts en attente) :') }}
                        <strong class="text-dark">{{ number_format($pendingRevenueAmount, 0, ',', ' ') }} F</strong>
                        <a href="{{ route('admin.revenues.index', ['tab' => 'pending']) }}" class="d-block mt-1">{{ __('Voir les lignes en attente') }} →</a>
                    </div>
                @endif
            </div>

            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-white py-3 border-bottom"><h6 class="m-0 fw-bold text-primary">{{ __('Nouvelles inscriptions (12 mois)') }}</h6></div>
                <div class="card-body">
                    <div class="chart-pie pt-2" style="height: 250px;">
                        <canvas id="userChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-white py-3 border-bottom"><h6 class="m-0 fw-bold text-primary">{{ __('Derniers utilisateurs') }}</h6></div>
                <div class="card-body">
                    @forelse($latestUsers as $user)
                        <a href="{{ route('admin.users.show', $user) }}" class="d-flex align-items-center mb-3 text-decoration-none text-gray-800">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle me-3 flex-shrink-0" style="width: 40px; height: 40px; object-fit: cover;">
                            <div class="min-width-0">
                                <h6 class="mb-0 small fw-bold text-truncate">{{ $user->name }}</h6>
                                <small class="text-muted">{{ $user->role }} — {{ $user->created_at->diffForHumans() }}</small>
                            </div>
                        </a>
                    @empty
                        <p class="text-center text-muted small m-0 py-3">{{ __('Aucun nouvel utilisateur.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-white py-3 border-bottom"><h6 class="m-0 fw-bold text-primary">{{ __('Derniers livres') }}</h6></div>
                <div class="card-body">
                    @forelse($latestBooks as $book)
                        <a href="{{ route('admin.books.show', $book) }}" class="d-flex align-items-center mb-3 text-decoration-none text-gray-800">
                            <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}" class="rounded me-3 flex-shrink-0" style="width: 40px; height: 55px; object-fit: cover;">
                            <div class="min-width-0">
                                <h6 class="mb-0 small fw-bold text-truncate">{{ $book->title }}</h6>
                                <small class="text-muted">{{ $book->author->name ?? '—' }} — {{ $book->created_at->diffForHumans() }}</small>
                            </div>
                        </a>
                    @empty
                        <p class="text-center text-muted small m-0 py-3">{{ __('Aucun nouveau livre.') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="card border-0 shadow mb-4">
                <div class="card-header bg-white py-3 border-bottom"><h6 class="m-0 fw-bold text-primary">{{ __('Derniers avis') }}</h6></div>
                <div class="card-body">
                    @forelse($latestReviews as $review)
                        <a href="{{ route('admin.reviews.show', $review) }}" class="d-block mb-3 text-decoration-none text-gray-800">
                            <h6 class="mb-0 small fw-bold">{{ $review->user->name ?? '—' }} <span class="fw-normal text-muted">{{ __('sur') }}</span> {{ $review->book->title ?? '—' }}</h6>
                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                            <p class="small mb-0 mt-1 text-muted">{{ Str::limit($review->body, 60) }}</p>
                        </a>
                    @empty
                        <p class="text-center text-muted small m-0 py-3">{{ __('Aucun avis récent.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var revenueCtx = document.getElementById("revenueChart");
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($revenueChart['labels']),
                datasets: [{
                    label: @json(__('Revenus')),
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 2,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    data: @json($revenueChart['data']),
                }],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                     x: { grid: { display: false } },
                     y: { ticks: { beginAtZero: true, callback: value => value + ' F' } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: context => context.dataset.label + ': ' + context.parsed.y + ' F' } }
                }
            }
        });
    }

    var userCtx = document.getElementById("userChart");
    if (userCtx) {
        new Chart(userCtx, {
            type: 'bar',
            data: {
                labels: @json($userChart['labels']),
                datasets: [{
                    label: @json(__('Nouveaux utilisateurs')),
                    backgroundColor: "#4e73df",
                    hoverBackgroundColor: "#2e59d9",
                    borderColor: "#4e73df",
                    data: @json($userChart['data']),
                }],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                     x: { grid: { display: false } },
                     y: { ticks: { beginAtZero: true, precision: 0 } }
                },
                plugins: {
                    legend: { display: false },
                }
            }
        });
    }
});
</script>
@endpush
