@extends('layouts.dashboard')

@section('title', 'Gestion des Paiements')
@section('header', 'Gestion des Paiements')

@section('content')
<div class="container-fluid">
    <!-- Stat Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-left-primary shadow py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Montant Total (Complété)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_amount'], 2) }} €</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-left-success shadow py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paiements Aujourd'hui</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-left-info shadow py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Montant Aujourd'hui</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['today_amount'], 2) }} €</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-left-warning shadow py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Paiements (Total)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_count'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item"><a class="nav-link @if($currentTab === 'all') active @endif" href="{{ route('admin.payments.index', ['tab' => 'all', 'search' => $search]) }}">Tous</a></li>
                    <li class="nav-item"><a class="nav-link @if($currentTab === 'completed') active @endif" href="{{ route('admin.payments.index', ['tab' => 'completed', 'search' => $search]) }}">Complétés</a></li>
                    <li class="nav-item"><a class="nav-link @if($currentTab === 'pending') active @endif" href="{{ route('admin.payments.index', ['tab' => 'pending', 'search' => $search]) }}">En attente</a></li>
                    <li class="nav-item"><a class="nav-link @if($currentTab === 'failed') active @endif" href="{{ route('admin.payments.index', ['tab' => 'failed', 'search' => $search]) }}">Échoués</a></li>
                </ul>
                <div class="col-md-4">
                    <form action="{{ route('admin.payments.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Rechercher (email, ID...)" value="{{ $search ?? '' }}">
                            <input type="hidden" name="tab" value="{{ $currentTab }}">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Date</th>
                            <th>Utilisateur</th>
                            <th>Description</th>
                            <th class="text-end">Montant</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $payment->user->name ?? 'N/A' }} <br> <small class="text-muted">{{ $payment->user->email ?? '' }}</small></td>
                                <td>
                                    {{ $payment->payment_type }}
                                    @if($payment->book)
                                        <br><small class="text-muted">Livre: {{ $payment->book->title }}</small>
                                    @elseif($payment->subscription && $payment->subscription->subscriptionPlan)
                                        <br><small class="text-muted">Abo: {{ $payment->subscription->subscriptionPlan->name }}</small>
                                    @else
                                        <br><small class="text-muted">Type de paiement: {{ $payment->payment_type ?? 'N/A' }}</small>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($payment->amount, 2) }} {{ strtoupper($payment->currency) }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ \App\Helpers\StatusHelper::paymentStatusColor($payment->status) }}">{{ ucfirst($payment->status) }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-info btn-sm" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($payment->status === 'pending')
                                        <form action="{{ route('admin.payments.validate', $payment) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" title="Valider"><i class="fas fa-check"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Aucun paiement trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
