@extends('layouts.author')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">{{ __('Demande de versement') }}</h4>
                </div>
                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="mb-4 p-3 bg-light rounded">
                        <h5 class="text-primary">{{ __('Solde disponible pour le versement') }}</h5>
                        <p class="display-4 fw-bold">{{ number_format($availableBalance, 0, ',', ' ') }} FCFA</p>
                        <small class="text-muted">{{ __("Ce montant correspond à vos revenus approuvés qui n'ont pas encore été versés.") }}</small>
                    </div>

                    @if($pendingPayout)
                        <div class="alert alert-info">
                            <h5 class="alert-heading">{{ __('Demande en attente') }}</h5>
                            <p>{{ __("Vous avez déjà une demande de versement en attente de traitement par l'administration.") }}</p>
                            <hr>
                            <p class="mb-0">
                                <strong>{{ __('Montant :') }}</strong> {{ number_format($pendingPayout->amount, 0, ',', ' ') }} FCFA<br>
                                <strong>{{ __('Date de la demande :') }}</strong> {{ $pendingPayout->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                    @elseif($availableBalance < $minimumPayout)
                        <div class="alert alert-warning">
                            <h5 class="alert-heading">{{ __('Solde insuffisant') }}</h5>
                            <p>{{ __('Le montant minimum pour demander un versement est de') }} <strong>{{ number_format($minimumPayout, 0, ',', ' ') }} FCFA</strong>{{ __('. Continuez à promouvoir vos œuvres !') }}</p>
                        </div>
                    @else
                        <form action="{{ route('author.revenues.payout.submit') }}" method="POST">
                            @csrf
                            <p>{{ __('Veuillez choisir votre méthode de paiement et fournir les détails nécessaires pour le versement de votre solde de') }} <strong>{{ number_format($availableBalance, 0, ',', ' ') }} FCFA</strong>.</p>

                            <div class="mb-3">
                                <label for="payment_method" class="form-label">{{ __('Méthode de paiement') }}</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="mobile_money">Mobile Money (Orange, Moov, etc.)</option>
                                    <option value="bank_transfer">{{ __('Virement Bancaire') }}</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="payment_details" class="form-label">{{ __('Détails du paiement') }}</label>
                                <input type="text" class="form-control" id="payment_details" name="payment_details" placeholder="{{ __('Numéro de téléphone ou détails du compte') }}" required>
                                <div class="form-text">Indiquez le numéro de téléphone pour Mobile Money ou vos coordonnées bancaires (RIB) pour un virement.</div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">{{ __('Envoyer ma demande de versement') }}</button>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
