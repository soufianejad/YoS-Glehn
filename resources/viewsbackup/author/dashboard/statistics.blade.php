@extends('layouts.author')

@section('title', 'Statistiques Détaillées')
@section('header', 'Statistiques Détaillées')

@php
    // Helper function to format seconds into a readable string
    function format_seconds($seconds) {
        if ($seconds < 60) {
            return $seconds . 's';
        }
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        if ($hours > 0) {
            return "{$hours}h {$minutes}min";
        }
        return "{$minutes}min";
    }

    // Helper function to display star ratings
    function render_stars($rating, $max = 5) {
        $full_star = '<i class="fas fa-star text-warning"></i>';
        $half_star = '<i class="fas fa-star-half-alt text-warning"></i>';
        $empty_star = '<i class="far fa-star text-warning"></i>';
        $stars = '';
        $rating = floatval($rating);

        for ($i = 1; $i <= $max; $i++) {
            if ($rating >= $i) {
                $stars .= $full_star;
            } elseif ($rating > ($i - 1) && $rating < $i) {
                $stars .= $half_star;
            } else {
                $stars .= $empty_star;
            }
        }
        return $stars;
    }
@endphp

@section('content')
<div class="container-fluid">

    <!-- Global Stats -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-start-primary shadow-sm">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Revenus Totaux</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($globalStats['total_revenue'], 2) }} €</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-start-success shadow-sm">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Ventes Totales</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $globalStats['total_sales'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-start-info shadow-sm">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Note Moyenne Globale</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800 d-flex align-items-center">
                        {{ number_format($globalStats['overall_avg_rating'], 2) }} 
                        <span class="ms-2">{!! render_stars($globalStats['overall_avg_rating']) !!}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-start-warning shadow-sm">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Avis</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $globalStats['total_reviews'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Revenus des 6 derniers mois</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Ventes des 6 derniers mois</h6>
                </div>
                <div class="card-body">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 5 Lists Row -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Top 5 des livres les plus vendus</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($topSellingBooks as $book)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $book->title }}
                                <span class="badge bg-primary rounded-pill">{{ $book->purchases_count }} ventes</span>
                            </li>
                        @empty
                            <li class="list-group-item">Aucune vente enregistrée.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Top 5 des livres les plus lus</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($topReadBooks as $book)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $book->title }}
                                <span class="badge bg-success rounded-pill">{{ format_seconds($book->total_read) }}</span>
                            </li>
                        @empty
                            <li class="list-group-item">Aucun temps de lecture enregistré.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Per-Book Stats Table -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Statistiques détaillées par Livre</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Livre</th>
                            <th class="text-center">Ventes</th>
                            <th class="text-center">Temps de Lecture Total</th>
                            <th class="text-center">Temps d'Écoute Total</th>
                            <th class="text-center">Avis</th>
                            <th class="text-center">Note Moyenne</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}" style="width: 40px; height: 60px; object-fit: cover;" class="me-3 rounded">
                                        <div>
                                            <p class="mb-0 font-weight-bold">{{ $book->title }}</p>
                                            <small class="text-muted">Publié le: {{ $book->created_at->format('d/m/Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center align-middle">{{ $book->sales_count }}</td>
                                <td class="text-center align-middle">{{ format_seconds($book->total_time_read_seconds) }}</td>
                                <td class="text-center align-middle">{{ format_seconds($book->total_time_listened_seconds) }}</td>
                                <td class="text-center align-middle">{{ $book->reviews_count }}</td>
                                <td class="text-center align-middle">
                                    @if($book->avg_rating)
                                        {{ number_format($book->avg_rating, 2) }} {!! render_stars($book->avg_rating) !!}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Vous n'avez pas encore de livres publiés.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $books->links() }}
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($chartData);

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Revenus (€)',
                    data: chartData.revenue,
                    borderColor: 'rgba(78, 115, 223, 1)',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Ventes',
                    data: chartData.sales,
                    backgroundColor: 'rgba(28, 200, 138, 0.7)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>
@endpush
