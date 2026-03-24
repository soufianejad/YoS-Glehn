@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow border-0 rounded-4 p-5">
                <div class="mb-4 text-warning">
                    <i class="fas fa-spinner fa-spin fa-5x"></i>
                </div>
                <h2 class="fw-bold mb-3">{{ __('Paiement en attente') }}</h2>
                <p class="text-muted mb-4">{{ __('Veuillez valider l\'opération sur votre téléphone en saisissant votre code secret si nécessaire. Ne fermez pas cette page tant que l\'opération n\'est pas terminée.') }}</p>
                <div class="spinner-grow text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Optionnel: Ajouter une vérification AJAX ici pour rediriger automatiquement quand le statut change
    setInterval(() => {
        // Logique de vérification de statut ici
    }, 5000);
</script>
@endpush
@endsection
