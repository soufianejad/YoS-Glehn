@extends('layouts.dashboard')

@section('title', __('Progression des Quiz'))
@section('header', __('Ma Progression des Quiz'))

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Quiz Scores Chart -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Scores des Quiz Récents') }}</h6>
        </div>
        <div class="card-body">
            <div class="chart-area" style="height: 250px;">
                <canvas id="quizScoresChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quiz Attempts Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Historique des tentatives') }}</h6>
            <div class="col-md-4">
                <form action="{{ route('student.progress.quizzes') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('Rechercher un quiz...') }}" value="{{ $search ?? '' }}">
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
                            <th>{{ __('Quiz') }}</th>
                            <th>{{ __('Score') }}</th>
                            <th class="text-center">{{ __('Statut') }}</th>
                            <th class="text-center">{{ __('Date') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quizAttempts as $attempt)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3">
                                            <h6 class="mb-0 font-weight-bold">{{ $attempt->quiz->title ?? 'N/A' }}</h6>
                                            <small class="text-muted">{{ $attempt->quiz->book->title ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <h6 class="mb-0">{{ number_format($attempt->percentage, 0) }}%</h6>
                                    <small class="text-muted">{{ $attempt->correct_answers }} / {{ $attempt->total_questions }}</small>
                                </td>
                                <td class="text-center">
                                    @if($attempt->is_passed)
                                        <span class="badge badge-success">{{ __('Réussi') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('Échoué') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $attempt->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('student.quiz.results', $attempt->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye fa-sm"></i> {{ __('Voir') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">{{ __('Aucune tentative de quiz trouvée.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="d-flex justify-content-center mt-3">
                {{ $quizAttempts->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        Chart.defaults.font.family = "Nunito", '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        const ctx = document.getElementById('quizScoresChart').getContext('2d');
        const quizChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Score (%)',
                    data: @json($chartData->map(fn($score) => round($score))),
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    maxBarThickness: 30
                }]
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    y: { beginAtZero: true, max: 100, title: { display: true, text: 'Score (%)' } },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endsection