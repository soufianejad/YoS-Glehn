@extends('layouts.dashboard')

@section('title', __('Paramètres de Paiement'))
@section('header', __('Paramètres de Paiement'))

@php
    $paymentService = app(\App\Services\PaymentService::class);
    $config = $paymentService->getGlobalConfigurations();
    $countries = $config['countries'];
    $methods = $config['methods'];
@endphp

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Activation des modes de paiement par pays') }}</h6>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll()">{{ __('Tout sélectionner') }}</button>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.payment.update') }}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 200px;">{{ __('Pays') }}</th>
                                @foreach($methods as $methodCode => $method)
                                    <th class="text-center small">
                                        <div style="color: {{ $method['icon_color'] }}">{{ $method['name'] }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ $methodCode }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($countries as $iso => $country)
                                <tr>
                                    <td class="fw-bold">
                                        <span class="me-2">{{ $iso }}</span>
                                        {{ $country['iso'] === 'FR' ? 'France' : ($iso === 'CI' ? 'Côte d\'Ivoire' : ($iso === 'SN' ? 'Sénégal' : $iso)) }}
                                        <div class="text-muted small fw-normal">{{ $country['currency'] }}</div>
                                    </td>
                                    @foreach($methods as $methodCode => $method)
                                        <td class="text-center">
                                            <div class="form-check d-inline-block">
                                                <input class="form-check-input payment-checkbox" type="checkbox" 
                                                       name="methods[{{ $iso }}][{{ $methodCode }}]" 
                                                       id="check_{{ $iso }}_{{ $methodCode }}"
                                                       {{ isset($payment_methods[$iso][$methodCode]) && $payment_methods[$iso][$methodCode] === 'on' ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold mb-3">{{ __('Clés API (Rappel)') }}</h6>
                        <p class="small text-muted">
                            {{ __('Note: Les clés API doivent être configurées dans le fichier .env pour que les paiements fonctionnent réellement.') }}
                        </p>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary px-5">
                        <i class="fas fa-save me-2"></i> {{ __('Enregistrer les modifications') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function selectAll() {
        const checkboxes = document.querySelectorAll('.payment-checkbox');
        const allChecked = Array.from(checkboxes).every(c => c.checked);
        checkboxes.forEach(c => c.checked = !allChecked);
    }
</script>
@endsection
