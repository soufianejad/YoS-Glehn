@extends('layouts.author')

@section('title', 'Statistiques pour : ' . $book->title)
@section('header', 'Statistiques du livre')

@php
    function format_seconds($seconds) {
        if (!$seconds || $seconds < 60) {
            return ($seconds ?? 0) . 's';
        }
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        if ($hours > 0) {
            return "{$hours}h {$minutes}min";
        }
        return "{$minutes}min";
    }

    function render_stars($rating, $max = 5) {
        $full_star = '<i class="fas fa-star text-warning"></i>';
        $half_star = '<i class="fas fa-star-half-alt text-warning"></i>';
        $empty_star = '<i class="far fa-star text-warning"></i>';
        $stars = '';
        $rating = floatval($rating);
        for ($i = 1; $i <= $max; $i++) {
            if ($rating >= $i) $stars .= $full_star;
            elseif ($rating > ($i - 1) && $rating < $i) $stars .= $half_star;
            else $stars .= $empty_star;
        }
        return $stars;
    }
@endphp

@section('content')
<div class="container-fluid">
    <!-- Book Header -->
    <div class="d-flex align-items-center mb-4">
        <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}" style="width: 80px; height: 120px; object-fit: cover;" class="me-4 rounded shadow-sm">
        <div>
            <h1 class="h3 mb-0">{{ $book->title }}</h1>
            <a href="{{ route('author.books.index') }}" class="btn btn-sm btn-outline-secondary mt-2">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
        </div>
    </div>

    <!-- Key Stats -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-start-primary shadow-sm"><div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Revenus</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['revenue'], 2) }} €</div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-start-success shadow-sm"><div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Ventes</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['sales'] }}</div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-start-info shadow-sm"><div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Note Moyenne</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800 d-flex align-items-center">
                    {{ number_format($stats['avg_rating'], 2) }} <span class="ms-2">{!! render_stars($stats['avg_rating']) !!}</span>
                </div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-start-warning shadow-sm"><div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Avis</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['reviews_count'] }}</div>
            </div></div>
        </div>
    </div>

    <!-- Chart -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Ventes des 6 derniers mois</h6></div>
                <div class="card-body">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Engagement Tables -->
    <div class="row">
        <!-- Readers Table -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Progression des Lecteurs (PDF)</h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead><tr><th>Lecteur</th><th>Progression</th><th>Temps passé</th></tr></thead>
                            <tbody>
                                @forelse ($readers as $progress)
                                    <tr>
                                        <td>{{ $progress->user->name }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $progress->progress_percentage }}%;" aria-valuenow="{{ $progress->progress_percentage }}" aria-valuemin="0" aria-valuemax="100">{{ round($progress->progress_percentage) }}%</div>
                                            </div>
                                        </td>
                                        <td>{{ format_seconds($progress->time_spent) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">Aucun lecteur pour le moment.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">{{ $readers->links() }}</div>
                </div>
            </div>
        </div>

        <!-- Listeners Table -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Progression des Auditeurs (Audio)</h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead><tr><th>Auditeur</th><th>Progression</th></tr></thead>
                            <tbody>
                                @forelse ($listeners as $progress)
                                    <tr>
                                        <td>{{ $progress->user->name }}</td>
                                        <td>
                                            @php $percentage = $book->audio_duration > 0 ? ($progress->current_position / $book->audio_duration) * 100 : 0; @endphp
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">{{ round($percentage) }}%</div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center text-muted">Aucun auditeur pour le moment.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">{{ $listeners->links() }}</div>
                </div>
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
            options: { scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });
    });
</script>
@endpush
