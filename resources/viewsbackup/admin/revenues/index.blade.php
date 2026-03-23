@extends('layouts.dashboard')

@section('title', 'Gestion des Revenus')
@section('header', 'Gestion des Revenus')

@section('content')
<div class="container-fluid">
    <!-- Stat Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-left-primary shadow py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Revenus (Total)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total'], 2) }} €</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-left-success shadow py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Revenus (Mois Actuel)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_current_month'], 2) }} €</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-left-warning shadow py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Revenus en Attente</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-left-info shadow py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Paiements Effectués</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_payouts'], 2) }} €</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenues Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link @if($currentTab === 'pending') active @endif" href="{{ route('admin.revenues.index', ['tab' => 'pending']) }}">
                        En Attente <span class="badge bg-warning">{{ $stats['pending_count'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($currentTab === 'approved') active @endif" href="{{ route('admin.revenues.index', ['tab' => 'approved']) }}">Approuvés</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($currentTab === 'paid') active @endif" href="{{ route('admin.revenues.index', ['tab' => 'paid']) }}">Payés</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if($currentTab === 'all') active @endif" href="{{ route('admin.revenues.index', ['tab' => 'all']) }}">Tous</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>Date</th>
                            <th>Auteur</th>
                            <th>Livre</th>
                            <th class="text-end">Montant Total</th>
                            <th class="text-end">Part Auteur</th>
                            <th class="text-end">Part Plateforme</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenues as $revenue)
                            <tr>
                                <td>{{ $revenue->created_at->format('d/m/Y') }}</td>
                                <td>{{ $revenue->author->name ?? 'N/A' }}</td>
                                <td>{{ $revenue->book->title ?? 'N/A' }}</td>
                                <td class="text-end">{{ number_format($revenue->total_amount, 2) }} €</td>
                                <td class="text-end">{{ number_format($revenue->author_amount, 2) }} €</td>
                                <td class="text-end">{{ number_format($revenue->platform_amount, 2) }} €</td>
                                <td class="text-center">{{ $revenue->revenue_type }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ \App\Helpers\StatusHelper::revenueStatusColor($revenue->status) }}">{{ ucfirst($revenue->status) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($revenue->status === 'pending')
                                        <form action="{{ route('admin.revenues.approve', $revenue) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.revenues.edit', $revenue) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">Aucun revenu trouvé pour cet onglet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $revenues->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
