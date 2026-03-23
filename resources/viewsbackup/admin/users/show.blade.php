@extends('layouts.dashboard')

@section('title', 'Détail de l\'Utilisateur')
@section('header', 'Profil de l\'Utilisateur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Profile Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <img class="img-fluid rounded-circle mb-3" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" style="width: 150px; height: 150px; object-fit: cover;">
                    <h4 class="card-title mb-0">{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    <span class="badge badge-{{ \App\Helpers\StatusHelper::userRoleColor($user->role) }}">{{ ucfirst($user->role) }}</span>
                    @if($user->is_active)
                        <span class="badge bg-success">Actif</span>
                    @else
                        <span class="badge bg-danger">Inactif</span>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">Modifier</a>
                    <a href="{{ route('admin.users.impersonate', $user) }}" class="btn btn-info btn-sm">Impersonate</a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="col-xl-8 col-lg-7">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2"><div class="card-body">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Dépensé</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_spent'], 2) }} €</div>
                    </div></div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2"><div class="card-body">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Achats de Livres</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['purchases_count'] }}</div>
                    </div></div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2"><div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Quiz Tentés</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['quizzes_taken'] }}</div>
                    </div></div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2"><div class="card-body">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Score Moyen</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['avg_quiz_score'], 1) }}%</div>
                    </div></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Tabs -->
    <div class="card shadow">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="userActivityTab" role="tablist">
                <li class="nav-item" role="presentation"><button class="nav-link active" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">Paiements</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" id="quizzes-tab" data-bs-toggle="tab" data-bs-target="#quizzes" type="button" role="tab">Tentatives de Quiz</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" id="reading-tab" data-bs-toggle="tab" data-bs-target="#reading" type="button" role="tab">Progression Lecture</button></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="userActivityTabContent">
                <!-- Payments Tab -->
                <div class="tab-pane fade show active" id="payments" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th class="text-end">Montant</th>
                                    <th class="text-center">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($payment->book)
                                                Achat du livre: {{ $payment->book->title }}
                                            @elseif($payment->subscription && $payment->subscription->subscriptionPlan)
                                                Abonnement: {{ $payment->subscription->subscriptionPlan->name }}
                                            @else
                                                {{ $payment->payment_type }}
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($payment->amount, 2) }} €</td>
                                        <td class="text-center"><span class="badge badge-{{ \App\Helpers\StatusHelper::paymentStatusColor($payment->status) }}">{{ ucfirst($payment->status) }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">Aucun paiement trouvé.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $payments->links('pagination::bootstrap-5') }}
                </div>

                <!-- Quizzes Tab -->
                <div class="tab-pane fade" id="quizzes" role="tabpanel">
                     <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Quiz</th>
                                    <th>Date</th>
                                    <th class="text-center">Score</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quizAttempts as $attempt)
                                    <tr>
                                        <td>{{ $attempt->quiz->title }} <br> <small class="text-muted">{{ $attempt->quiz->book->title }}</small></td>
                                        <td>{{ $attempt->completed_at?->format('d/m/Y H:i') }}</td>
                                        <td class="text-center align-middle">{{ round($attempt->percentage) }}%</td>
                                        <td class="text-center align-middle">
                                            @if($attempt->is_passed)
                                                <span class="badge bg-success">Réussi</span>
                                            @else
                                                <span class="badge bg-danger">Échoué</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            <a href="{{ route('teacher.progress.quiz-attempt', $attempt) }}" class="btn btn-sm btn-info">Détails</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted">Aucune tentative de quiz trouvée.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $quizAttempts->links('pagination::bootstrap-5') }}
                </div>

                <!-- Reading Progress Tab -->
                <div class="tab-pane fade" id="reading" role="tabpanel">
                     <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Livre</th>
                                    <th style="width: 40%;">Progression</th>
                                    <th>Dernière lecture</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($readingProgress as $progress)
                                    <tr>
                                        <td>{{ $progress->book->title }}</td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $progress->percentage }}%;" aria-valuenow="{{ $progress->percentage }}">{{ round($progress->percentage) }}%</div>
                                            </div>
                                        </td>
                                        <td>{{ $progress->last_read_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">Aucune progression de lecture trouvée.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $readingProgress->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
