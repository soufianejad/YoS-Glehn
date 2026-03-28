@extends('layouts.dashboard')

@section('title', __('Statistiques'))
@section('header', __('Statistiques détaillées'))

@push('styles')
<style>
    .stat-kpi-card {
        border-radius: 0.5rem;
        border: 0;
        border-left: 4px solid;
        transition: box-shadow 0.2s ease;
    }
    .stat-kpi-card:hover {
        box-shadow: 0 0.35rem 0.85rem rgba(0, 0, 0, 0.08) !important;
    }
    .chart-wrap {
        position: relative;
        height: 300px;
        width: 100%;
    }
    @media (min-width: 992px) {
        .chart-wrap.chart-tall {
            height: 340px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-2">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <p class="text-muted small mb-0">
                {{ __('Période des graphiques : :n derniers mois (mois complets alignés). Les revenus comptent uniquement les paiements validés (complétés + date d’encaissement).', ['n' => $monthsWindow]) }}
            </p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>{{ __('Retour au tableau de bord') }}
        </a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card shadow-sm stat-kpi-card border-start border-primary">
                <div class="card-body py-3">
                    <div class="text-uppercase text-muted small fw-bold mb-1">{{ __('Utilisateurs') }}</div>
                    <div class="h4 mb-0 text-dark">{{ number_format($summary['users_total'], 0, ',', ' ') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card shadow-sm stat-kpi-card border-start border-success">
                <div class="card-body py-3">
                    <div class="text-uppercase text-muted small fw-bold mb-1">{{ __('Livres (total)') }}</div>
                    <div class="h4 mb-0 text-dark">{{ number_format($summary['books_total'], 0, ',', ' ') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card shadow-sm stat-kpi-card border-start border-info">
                <div class="card-body py-3">
                    <div class="text-uppercase text-muted small fw-bold mb-1">{{ __('Publiés') }}</div>
                    <div class="h4 mb-0 text-dark">{{ number_format($summary['books_published'], 0, ',', ' ') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card shadow-sm stat-kpi-card border-start border-warning">
                <div class="card-body py-3">
                    <div class="text-uppercase text-muted small fw-bold mb-1">{{ __('Paiements validés') }}</div>
                    <div class="h4 mb-0 text-dark">{{ number_format($summary['payments_validated'], 0, ',', ' ') }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card shadow-sm stat-kpi-card border-start border-danger">
                <div class="card-body py-3">
                    <div class="text-uppercase text-muted small fw-bold mb-1">{{ __('Revenus (total)') }}</div>
                    <div class="h5 mb-0 text-dark">{{ number_format($summary['revenue_total'], 0, ',', ' ') }} <span class="small text-muted">F</span></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4 col-xl-2">
            <div class="card shadow-sm stat-kpi-card border-start border-secondary">
                <div class="card-body py-3">
                    <div class="text-uppercase text-muted small fw-bold mb-1">{{ __('Revenus (année)') }}</div>
                    <div class="h5 mb-0 text-dark">{{ number_format($summary['revenue_year'], 0, ',', ' ') }} <span class="small text-muted">F</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-user-plus me-2"></i>{{ __('Nouveaux utilisateurs par mois') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-wrap">
                        <canvas id="usersChart" aria-label="{{ __('Graphique inscriptions') }}"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-success"><i class="fas fa-book me-2"></i>{{ __('Nouveaux livres par mois') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-wrap">
                        <canvas id="booksChart" aria-label="{{ __('Graphique livres') }}"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-coins me-2"></i>{{ __('Revenus encaissés par mois') }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-wrap chart-tall">
                        <canvas id="revenueChart" aria-label="{{ __('Graphique revenus') }}"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: true, position: 'top' },
        },
    };

    const labels = @json($chartLabels);

    const usersCtx = document.getElementById('usersChart');
    if (usersCtx) {
        new Chart(usersCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: @json(__('Nouveaux utilisateurs')),
                    data: @json($userSeries),
                    backgroundColor: 'rgba(54, 162, 235, 0.45)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }],
            },
            options: {
                ...commonOptions,
                scales: {
                    x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 45 } },
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                },
            },
        });
    }

    const booksCtx = document.getElementById('booksChart');
    if (booksCtx) {
        new Chart(booksCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: @json(__('Nouveaux livres')),
                    data: @json($bookSeries),
                    backgroundColor: 'rgba(25, 135, 84, 0.35)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }],
            },
            options: {
                ...commonOptions,
                scales: {
                    x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 45 } },
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                },
            },
        });
    }

    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: @json(__('Revenus (FCFA)')),
                    data: @json($revenueSeries),
                    borderColor: 'rgba(78, 115, 223, 1)',
                    backgroundColor: 'rgba(78, 115, 223, 0.08)',
                    fill: true,
                    tension: 0.25,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                }],
            },
            options: {
                ...commonOptions,
                scales: {
                    x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 45 } },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return value + ' F';
                            },
                        },
                    },
                },
                plugins: {
                    ...commonOptions.plugins,
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                const v = ctx.parsed.y;
                                return (ctx.dataset.label || '') + ': ' + new Intl.NumberFormat(undefined, { maximumFractionDigits: 0 }).format(v) + ' F';
                            },
                        },
                    },
                },
            },
        });
    }
});
</script>
@endpush
