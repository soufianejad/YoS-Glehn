@extends('layouts.dashboard')

@section('title', __('Progression de Lecture'))
@section('header', __('Ma Progression de Lecture'))

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Reading Activity Chart -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Activité de lecture (30 derniers jours)') }}</h6>
        </div>
        <div class="card-body">
            <div class="chart-area" style="height: 250px;">
                <canvas id="readingActivityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Reading Progress Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Détails de la Progression') }}</h6>
            <div class="col-md-4">
                <form action="{{ route('student.progress.reading') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('Rechercher un livre...') }}" value="{{ $search ?? '' }}">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('Livre') }}</th>
                            <th>{{ __('Progression') }}</th>
                            <th class="text-center">{{ __('Temps Passé') }}</th>
                            <th class="text-center">{{ __('Dernière Lecture') }}</th>
                            <th class="text-center">{{ __('Statut') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($readingProgress as $progress)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $progress->book->cover_image_url }}" alt="{{ $progress->book->title }}" class="rounded mr-3" style="width: 40px; height: 55px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 font-weight-bold">{{ $progress->book->title ?? 'N/A' }}</h6>
                                            <small class="text-muted">{{ $progress->book->author->name ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td style="min-width: 150px;">
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $progress->progress_percentage }}%;" aria-valuenow="{{ $progress->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted">{{ number_format($progress->progress_percentage, 0) }}%</small>
                                </td>
                                <td class="text-center">{{ floor($progress->time_spent / 60) }} min</td>
                                <td class="text-center">{{ $progress->last_read_at ? $progress->last_read_at->format('d/m/Y') : 'N/A' }}</td>
                                <td class="text-center">{!! $progress->completed_at ? '<span class="badge badge-success">Terminé</span>' : '<span class="badge badge-secondary">En cours</span>' !!}</td>
                                <td class="text-center">
                                    @if($progress->book)
                                        <a href="{{ route('read.book', $progress->book->slug) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-book-open fa-sm"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">{{ __('Aucune progression de lecture. Commencez à lire un livre !') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="d-flex justify-content-center mt-3">
                {{ $readingProgress->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        Chart.defaults.font.family = "Nunito", '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        const ctx = document.getElementById('readingActivityChart').getContext('2d');
        const readingChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Temps passé (minutes)',
                    data: @json($chartData->map(fn($seconds) => round($seconds / 60))),
                    borderColor: 'rgba(78, 115, 223, 1)',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Temps passé (minutes)' } },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: context => `${context.dataset.label}: ${context.parsed.y} min`
                        }
                    }
                }
            }
        });
    });
</script>
@endsection