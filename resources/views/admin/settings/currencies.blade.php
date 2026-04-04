@extends('layouts.dashboard')

@section('title', __('Gestion des devises'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Settings Sidebar -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-0">
                    <div class="list-group list-group-flush rounded">
                        <a href="{{ route('admin.settings.general') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.general') ? 'active' : '' }}">
                            <i class="fas fa-cogs fa-fw me-2"></i> {{ __('Général') }}
                        </a>
                        <a href="{{ route('admin.settings.appearance') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.appearance') ? 'active' : '' }}">
                            <i class="fas fa-paint-brush fa-fw me-2"></i> {{ __('Apparence') }}
                        </a>
                        <a href="{{ route('admin.settings.email') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.email') ? 'active' : '' }}">
                            <i class="fas fa-envelope fa-fw me-2"></i> {{ __('Email (SMTP)') }}
                        </a>
                        <a href="{{ route('admin.settings.payment') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.payment') ? 'active' : '' }}">
                            <i class="fas fa-credit-card fa-fw me-2"></i> {{ __('Paiements & Pays') }}
                        </a>
                        <a href="{{ route('admin.settings.languages') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.languages') ? 'active' : '' }}">
                            <i class="fas fa-language fa-fw me-2"></i> {{ __('Langues') }}
                        </a>
                        <a href="{{ route('admin.settings.currencies') }}" class="list-group-item list-group-item-action {{ request()->routeIs('admin.settings.currencies') ? 'active' : '' }}">
                            <i class="fas fa-money-bill fa-fw me-2"></i> {{ __('Devises') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-money-bill me-2"></i>{{ __('Devises disponibles') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <h6 class="font-weight-bold mb-3">{{ __('Ajouter une devise') }}</h6>
                            <form action="{{ route('admin.settings.currencies.update') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="currency_code" class="form-label">{{ __('Code de la devise') }}</label>
                                    <input type="text" class="form-control @error('currency_code') is-invalid @enderror" id="currency_code" name="currency_code" placeholder="{{ __('ex: XOF, EUR, USD') }}" required>
                                    @error('currency_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label for="currency_name" class="form-label">{{ __('Nom de la devise') }}</label>
                                    <input type="text" class="form-control @error('currency_name') is-invalid @enderror" id="currency_name" name="currency_name" placeholder="{{ __('ex: Franc CFA, Euro, US Dollar') }}" required>
                                    @error('currency_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label for="exchange_rate" class="form-label">{{ __('Taux de change (par rapport à XOF)') }}</label>
                                    <input type="number" step="0.0001" class="form-control @error('exchange_rate') is-invalid @enderror" id="exchange_rate" name="exchange_rate" placeholder="{{ __('ex: 1.0') }}" value="1" required>
                                    @error('exchange_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <button type="submit" name="add_currency" class="btn btn-primary w-100">{{ __('Ajouter la devise') }}</button>
                            </form>
                        </div>

                        <div class="col-md-8">
                            <h6 class="font-weight-bold mb-3">{{ __('Devises actuelles') }}</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Code') }}</th>
                                            <th>{{ __('Nom') }}</th>
                                            <th>{{ __('Taux de change (XOF)') }}</th>
                                            <th class="text-end">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($currencies as $currency)
                                            <tr>
                                                <td><strong>{{ $currency['code'] }}</strong></td>
                                                <td>{{ $currency['name'] }}</td>
                                                <td>{{ $currency['exchange_rate'] ?? 1 }}</td>
                                                <td class="text-end">
                                                    <form action="{{ route('admin.settings.currencies.update') }}" method="POST" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer cette devise ?') }}');">
                                                        @csrf
                                                        <input type="hidden" name="currency_code" value="{{ $currency['code'] }}">
                                                        <button type="submit" name="remove_currency" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    <i class="fas fa-info-circle mb-2 d-block fa-2x"></i>
                                                    {{ __('Aucune devise configurée pour le moment.') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
