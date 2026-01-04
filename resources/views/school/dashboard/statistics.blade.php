<!-- resources/views/school/dashboard/statistics.blade.php -->

@extends('layouts.school')

@section('content')
<div class="container">
    <h1>{{ __('School Statistics - ') }} {{ $school->name }}</h1>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">{{ __('Students Added by Month') }}</div>
                <div class="card-body">
                    <canvas id="studentsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">{{ __('Book Assignments by Month') }}</div>
                <div class="card-body">
                    <canvas id="assignmentsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Students Chart
    const studentsCtx = document.getElementById('studentsChart').getContext('2d');
    new Chart(studentsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($studentsByMonth->pluck('month')) !!},
            datasets: [{
                label: '{{ __("New Students") }}',
                data: {!! json_encode($studentsByMonth->pluck('count')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Book Assignments Chart
    const assignmentsCtx = document.getElementById('assignmentsChart').getContext('2d');
    new Chart(assignmentsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($bookAssignmentsByMonth->pluck('month')) !!},
            datasets: [{
                label: '{{ __("New Book Assignments") }}',
                data: {!! json_encode($bookAssignmentsByMonth->pluck('count')) !!},
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1,
                fill: false
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
