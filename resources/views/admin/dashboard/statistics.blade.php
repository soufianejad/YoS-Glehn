<!-- resources/views/admin/dashboard/statistics.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Admin Statistics') }}</h1>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">{{ __('Users Registered by Month') }}</div>
                <div class="card-body">
                    <canvas id="usersChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">{{ __('Books Added by Month') }}</div>
                <div class="card-body">
                    <canvas id="booksChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">{{ __('Revenue by Month') }}</div>
                <div class="card-body">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Users Chart
    const usersCtx = document.getElementById('usersChart').getContext('2d');
    new Chart(usersCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($usersByMonth->pluck('month')) !!},
            datasets: [{
                label: '{{ __("New Users") }}',
                data: {!! json_encode($usersByMonth->pluck('count')) !!},
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

    // Books Chart
    const booksCtx = document.getElementById('booksChart').getContext('2d');
    new Chart(booksCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($booksByMonth->pluck('month')) !!},
            datasets: [{
                label: '{{ __("New Books") }}',
                data: {!! json_encode($booksByMonth->pluck('count')) !!},
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                borderColor: 'rgba(75, 192, 192, 1)',
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

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($revenueByMonth->pluck('month')) !!},
            datasets: [{
                label: '{{ __("Revenue") }}',
                data: {!! json_encode($revenueByMonth->pluck('total_amount')) !!},
                backgroundColor: 'rgba(153, 102, 255, 0.5)',
                borderColor: 'rgba(153, 102, 255, 1)',
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
