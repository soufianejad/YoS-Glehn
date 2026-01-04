@extends('layouts.dashboard')

@section('title', __('Tableau de Bord Auteur'))
@section('header', __('Mon Tableau de Bord Auteur'))

@push('styles')
<style>
    .stat-card {
        background-color: #fff;
        border-radius: 0.75rem;
        padding: 1.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #e3e6f0;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    }
    .stat-icon {
        font-size: 2.5rem;
    }
    .action-card {
        text-align: center;
        padding: 1.5rem;
        border-radius: 0.75rem;
        background-color: #f8f9fc;
        border: 1px solid #e3e6f0;
        transition: all 0.3s ease;
    }
    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
        border-color: var(--primary-color);
    }
    .action-card .icon {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }
    .star-rating {
        color: #ffc107;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Bonjour,') }} {{ Auth::user()->first_name }}!</h1>
        <a href="{{ route('author.books.create') }}" class="btn btn-sm btn-primary shadow-sm"><i class="fas fa-plus fa-sm text-white-50"></i> {{__('Publier un nouveau livre')}}</a>
    </div>

    <!-- Key Metrics Section -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card h-100 d-flex align-items-center">
                <div class="stat-icon mr-3 text-primary"><i class="fas fa-dollar-sign"></i></div>
                <div>
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __("Chiffre d'affaires (Total)") }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalRevenue, 2) }} F</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card h-100 d-flex align-items-center">
                <div class="stat-icon mr-3 text-success"><i class="fas fa-book"></i></div>
                <div>
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('Livres Publiés') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBooks }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card h-100 d-flex align-items-center">
                <div class="stat-icon mr-3 text-info"><i class="fas fa-shopping-cart"></i></div>
                <div>
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('Ventes') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lifetimeSales }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card h-100 d-flex align-items-center">
                <div class="stat-icon mr-3 text-warning"><i class="fas fa-star"></i></div>
                <div>
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Total Avis') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalReviews }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Revenue Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __("Revenus des 6 derniers mois") }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recently Published Books Section -->
            <div class="card shadow mb-4">
                 <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Livres Récemment Publiés') }}</h6>
                </div>
                <div class="card-body">
                    @forelse($recentlyPublishedBooks as $book)
                        <div class="d-flex justify-content-between align-items-center p-3 mb-2 bg-light">
                            <div>
                                <h6 class="font-weight-bold mb-0">{{ $book->title }}</h6>
                                <div class="star-rating small">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $book->reviews_avg_rating ? 'text-warning' : 'text-gray-300' }}"></i>
                                    @endfor
                                    <span class="text-muted">({{ $book->reviews_count }})</span>
                                </div>
                            </div>
                            <a href="{{ route('author.books.show', $book->id) }}" class="btn btn-info btn-sm">{{ __('Détails') }}</a>
                        </div>
                    @empty
                         <p class="text-center text-muted">{{ __("Vous n'avez pas encore publié de livres.") }}</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Side Column -->
        <div class="col-xl-4 col-lg-5">
            <!-- Quick Actions -->
             <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Actions rapides') }}</h6>
                </div>
                <div class="card-body">
                   <a href="{{ route('author.books.create') }}" class="btn btn-primary btn-icon-split btn-block mb-2">
                        <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                        <span class="text">{{ __('Publier un livre') }}</span>
                    </a>
                    <a href="{{ route('author.revenues.index') }}" class="btn btn-success btn-icon-split btn-block mb-2">
                        <span class="icon text-white-50"><i class="fas fa-dollar-sign"></i></span>
                        <span class="text">{{ __('Voir mes revenus') }}</span>
                    </a>
                     <a href="{{ route('author.reviews') }}" class="btn btn-info btn-icon-split btn-block">
                        <span class="icon text-white-50"><i class="fas fa-star"></i></span>
                        <span class="text">{{ __('Gérer les avis') }}</span>
                    </a>
                </div>
            </div>
            <!-- Recent Reviews -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Avis Récents') }}</h6>
                </div>
                <div class="card-body">
                    @forelse($recentReviews as $review)
                        <div class="mb-3">
                            <div class="small text-gray-500">{{ $review->user->name }} {{ __('on') }} <strong>{{ $review->book->title }}</strong></div>
                            <div class="star-rating small">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                            <p class="text-gray-800 small mb-0 mt-1">"{{ Str::limit($review->review_text, 80) }}"</p>
                        </div>
                    @empty
                        <p class="text-center text-muted small">{{ __('Vous n\'avez aucun avis pour le moment.') }}</p>
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
    var ctx = document.getElementById("revenueChart");
    if (ctx) {
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($revenueChart['labels']),
                datasets: [{
                    label: "Revenus",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: @json($revenueChart['data']),
                }],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                     x: { grid: { display: false } },
                     y: { 
                         ticks: { 
                             beginAtZero: true,
                             callback: function(value, index, values) {
                                 return value + ' F';
                             }
                         } 
                     }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + ' F';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
