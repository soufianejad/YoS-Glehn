@extends('layouts.dashboard')

@section('title', __('Tableau de Bord Administrateur'))
@section('header', __('Tableau de Bord Administrateur'))

@push('styles')
<style>
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
   
    .quick-link-card {
        transition: all 0.2s ease-in-out;
    }
    .quick-link-card:hover {
        background-color: #f8f9fc;
        transform: scale(1.05);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Vue d\'ensemble de la plateforme') }}</h1>
        <a href="{{ route('admin.settings.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-cogs fa-sm text-white-50"></i> {{__('Paramètres')}}</a>
    </div>

    <!-- Key Metrics Section -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __("Revenus (Mois)") }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($monthlyRevenue, 0) }} F</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-calendar-day fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __("Revenus (Annuel)") }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($annualRevenue, 0) }} F</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('Utilisateurs') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
             <div class="card border-left-warning shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Livres') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBooks }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-book fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Row of Key Metrics -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">{{ __('Écoles') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSchools }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-school fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">{{ __('Auteurs') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalAuthors }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-pencil-alt fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">{{ __('Abonnements Actifs') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeSubscriptions }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-id-card fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 stat-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __("Revenus (Total)") }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalRevenue, 0) }} F</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-wallet fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content Column -->
        <div class="col-lg-8">
            <!-- Revenue Chart -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __("Aperçu des revenus") }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 320px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Management Sections -->
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">{{ __('Sections de gestion') }}</h6></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-4 text-center">
                            <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-gray-800 quick-link-card d-block py-3">
                                <i class="fas fa-users-cog fa-2x text-primary mb-2"></i>
                                <h6>{{ __('Utilisateurs') }}</h6>
                            </a>
                        </div>
                         <div class="col-lg-4 col-md-6 mb-4 text-center">
                            <a href="{{ route('admin.books.index') }}" class="text-decoration-none text-gray-800 quick-link-card d-block py-3">
                                <i class="fas fa-book-open fa-2x text-success mb-2"></i>
                                <h6>{{ __('Livres') }}</h6>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-4 text-center">
                            <a href="{{ route('admin.schools.index') }}" class="text-decoration-none text-gray-800 quick-link-card d-block py-3">
                                <i class="fas fa-school fa-2x text-info mb-2"></i>
                                <h6>{{ __('Écoles') }}</h6>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-4 text-center">
                            <a href="{{ route('admin.payments.index') }}" class="text-decoration-none text-gray-800 quick-link-card d-block py-3">
                                <i class="fas fa-credit-card fa-2x text-danger mb-2"></i>
                                <h6>{{ __('Paiements') }}</h6>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-4 text-center">
                            <a href="{{ route('admin.revenues.index') }}" class="text-decoration-none text-gray-800 quick-link-card d-block py-3">
                                <i class="fas fa-funnel-dollar fa-2x text-warning mb-2"></i>
                                <h6>{{ __('Revenus') }}</h6>
                            </a>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-4 text-center">
                            <a href="{{ route('admin.settings.index') }}" class="text-decoration-none text-gray-800 quick-link-card d-block py-3">
                                <i class="fas fa-cogs fa-2x text-secondary mb-2"></i>
                                <h6>{{ __('Paramètres') }}</h6>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Column -->
        <div class="col-lg-4">
            <!-- Action Required -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">{{ __('Action Requise') }}</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.books.pending') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-book fa-fw mr-2 text-gray-400"></i>{{ __('Livres en attente') }}</span>
                        <span class="badge badge-danger badge-pill">{{ $pendingBooks }}</span>
                    </a>
                    <a href="{{ route('admin.reviews.pending') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-star-half-alt fa-fw mr-2 text-gray-400"></i>{{ __('Avis en attente') }}</span>
                        <span class="badge badge-danger badge-pill">{{ $pendingReviews }}</span>
                    </a>
                    <a href="{{ route('admin.revenues.payouts.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                         <span><i class="fas fa-hand-holding-usd fa-fw mr-2 text-gray-400"></i>{{ __('Paiements en attente') }}</span>
                        <span class="badge badge-danger badge-pill">{{ $pendingPayouts }}</span>
                    </a>
                </div>
            </div>

            <!-- User Registrations Chart -->
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">{{ __("Inscriptions d'utilisateurs") }}</h6></div>
                <div class="card-body">
                    <div class="chart-pie pt-4" style="height: 250px;">
                        <canvas id="userChart"></canvas>
                    </div>
                </div>
            </div>

             <!-- Latest Users -->
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">{{ __('Derniers Utilisateurs') }}</h6></div>
                <div class="card-body">
                    @forelse($latestUsers as $user)
                        <a href="{{ route('admin.users.show', $user) }}" class="d-flex align-items-center mb-3 text-decoration-none text-gray-800">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle mr-3" style="width: 40px; height: 40px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0 small font-weight-bold">{{ $user->name }}</h6>
                                <small class="text-muted">{{ $user->role }} - {{ $user->created_at->diffForHumans() }}</small>
                            </div>
                        </a>
                    @empty
                        <p class="text-center text-muted small m-0">{{ __('Aucun nouvel utilisateur.') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Latest Books -->
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">{{ __('Derniers Livres') }}</h6></div>
                <div class="card-body">
                    @forelse($latestBooks as $book)
                        <a href="{{ route('admin.books.show', $book) }}" class="d-flex align-items-center mb-3 text-decoration-none text-gray-800">
                            <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}" class="rounded mr-3" style="width: 40px; height: 55px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0 small font-weight-bold">{{ $book->title }}</h6>
                                <small class="text-muted">{{ $book->author->name ?? 'N/A' }} - {{ $book->created_at->diffForHumans() }}</small>
                            </div>
                        </a>
                    @empty
                        <p class="text-center text-muted small m-0">{{ __('Aucun nouveau livre.') }}</p>
                    @endforelse
                </div>
            </div>

            <!-- Latest Reviews -->
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">{{ __('Derniers Avis') }}</h6></div>
                <div class="card-body">
                    @forelse($latestReviews as $review)
                        <a href="{{ route('admin.reviews.show', $review) }}" class="d-flex align-items-center mb-3 text-decoration-none text-gray-800">
                            <div class="mr-3">
                                <h6 class="mb-0 small font-weight-bold">{{ $review->user->name ?? 'N/A' }} sur {{ $review->book->title ?? 'N/A' }}</h6>
                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                <p class="small mb-0 mt-1">{{ Str::limit($review->body, 50) }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-center text-muted small m-0">{{ __('Aucun nouvel avis.') }}</p>
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
    // Revenue Chart
    var revenueCtx = document.getElementById("revenueChart");
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($revenueChart['labels']),
                datasets: [{
                    label: "Revenus",
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

    // User Chart
    var userCtx = document.getElementById("userChart");
    if (userCtx) {
        new Chart(userCtx, {
            type: 'bar',
            data: {
                labels: @json($userChart['labels']),
                datasets: [{
                    label: "Nouveaux Utilisateurs",
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