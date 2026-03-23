@extends('layouts.dashboard')

@section('title', __('Mes Revenus'))
@section('header', __('Mes Revenus'))

@section('content')
<div class="container-fluid">
    <!-- Key Metrics Section -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __("Gains Totaux") }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalEarnings, 2) }} F</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __("Total Versé") }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalPaid, 2) }} F</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __("Solde Actuel") }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalUnpaid, 2) }} F</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-wallet fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Historique des revenus') }}</h6>
                </div>
                 <div class="col-md-4">
                    <form action="{{ route('author.revenues.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('Rechercher...') }}" value="{{ $search ?? '' }}">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                        </div>
                    </form>
                </div>
                <div class="col text-right">
                    <a href="{{ route('author.revenues.payout.request') }}" class="btn btn-success btn-icon-split btn-sm">
                        <span class="icon text-white-50"><i class="fas fa-hand-holding-usd"></i></span>
                        <span class="text">{{ __('Demander un versement') }}</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('Livre') }}</th>
                            <th class="text-right">{{ __('Montant Total') }}</th>
                            <th class="text-right">{{ __('Ma Part') }}</th>
                            <th class="text-center">{{ __('Type') }}</th>
                            <th class="text-center">{{ __('Statut') }}</th>
                            <th class="text-center">{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenues as $revenue)
                            <tr>
                                <td>{{ $revenue->book->title ?? 'N/A' }}</td>
                                <td class="text-right">{{ number_format($revenue->total_amount, 2) }} F</td>
                                <td class="text-right font-weight-bold">{{ number_format($revenue->author_amount, 2) }} F</td>
                                <td class="text-center">{{ ucfirst(str_replace('_', ' ', $revenue->revenue_type)) }}</td>
                                <td class="text-center">
                                     <span class="badge badge-{{ \App\Helpers\StatusHelper::bookStatusColor($revenue->status) }}">{{ ucfirst($revenue->status) }}</span>
                                </td>
                                <td class="text-center">{{ $revenue->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">{{ __('Aucun revenu trouvé.') }}</td>
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
