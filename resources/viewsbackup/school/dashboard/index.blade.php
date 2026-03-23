@extends('layouts.dashboard')

@section('title', __('Tableau de Bord École'))
@section('header', __('Tableau de Bord de l\'École'))

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
        background-color: #fff;
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
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $school->name }}</h1>
        <a href="{{ route('school.settings') }}" class="btn btn-sm btn-info shadow-sm"><i class="fas fa-cogs fa-sm text-white-50"></i> {{__('Paramètres')}}</a>
    </div>

    <!-- Key Metrics Section -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card h-100 d-flex align-items-center">
                <div class="stat-icon mr-3 text-primary"><i class="fas fa-user-graduate"></i></div>
                <div>
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Étudiants') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card h-100 d-flex align-items-center">
                <div class="stat-icon mr-3 text-warning"><i class="fas fa-chalkboard-teacher"></i></div>
                <div>
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Enseignants') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTeachers }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card h-100 d-flex align-items-center">
                <div class="stat-icon mr-3 text-success"><i class="fas fa-school"></i></div>
                <div>
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('Classes') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalClasses }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card h-100 d-flex align-items-center">
                <div class="stat-icon mr-3 text-info"><i class="fas fa-book"></i></div>
                <div>
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('Livres Assignés') }}</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBookAssignments }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
             <h6 class="m-0 font-weight-bold text-primary">{{ __('Actions Rapides') }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('school.students.index') }}" class="text-decoration-none">
                        <div class="action-card h-100">
                            <div class="icon"><i class="fas fa-users-cog"></i></div>
                            <h6 class="font-weight-bold text-gray-800">{{ __('Gérer les Étudiants') }}</h6>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('school.classes.index') }}" class="text-decoration-none">
                        <div class="action-card h-100">
                            <div class="icon"><i class="fas fa-chalkboard"></i></div>
                            <h6 class="font-weight-bold text-gray-800">{{ __('Gérer les Classes') }}</h6>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('school.books.assignments.create') }}" class="text-decoration-none">
                        <div class="action-card h-100">
                            <div class="icon"><i class="fas fa-book-medical"></i></div>
                            <h6 class="font-weight-bold text-gray-800">{{ __('Assigner un Livre') }}</h6>
                        </div>
                    </a>
                </div>
                 <div class="col-lg-3 col-md-6 mb-3">
                    <a href="{{ route('school.announcements.create') }}" class="text-decoration-none">
                        <div class="action-card h-100">
                            <div class="icon"><i class="fas fa-bullhorn"></i></div>
                            <h6 class="font-weight-bold text-gray-800">{{ __('Nouvelle Annonce') }}</h6>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Student Growth Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __("Croissance des inscriptions d'étudiants") }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="studentGrowthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Activité Récente') }}</h6>
                </div>
                <div class="card-body">
                    @forelse($recentActivity as $activity)
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-circle bg-light mr-3">
                                @if($activity['type'] == 'new_student')
                                    <i class="fas fa-user-plus text-primary"></i>
                                @else
                                    <i class="fas fa-plus-square text-success"></i>
                                @endif
                            </div>
                            <div>
                                <div class="small text-gray-500">{{ $activity['timestamp']->diffForHumans() }}</div>
                                @if($activity['type'] == 'new_student')
                                    <span class="font-weight-bold">{{ __("Nouvel étudiant :") }} {{ $activity['data']->name }}</span>
                                @else
                                    <span class="font-weight-bold">{{ __("Nouvelle classe :") }} {{ $activity['data']->name }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">{{ __('Aucune activité récente.') }}</p>
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
    // Student Growth Chart
    var ctx = document.getElementById("studentGrowthChart");
    if (ctx) {
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($studentGrowthChart['labels']),
                datasets: [{
                    label: "Nouveaux Étudiants",
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
                    data: @json($studentGrowthChart['data']),
                }],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                scales: {
                    x: {
                        time: {
                            unit: 'month'
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
});
</script>
@endpush
