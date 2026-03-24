@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow border-0 rounded-4 p-5">
                <div class="mb-4 text-danger">
                    <i class="fas fa-times-circle fa-5x"></i>
                </div>
                <h2 class="fw-bold mb-3">{{ __('Échec du paiement') }}</h2>
                <p class="text-muted mb-4">{{ session('danger') ?? __('Une erreur est survenue lors du traitement de votre paiement. Veuillez réessayer.') }}</p>
                <a href="{{ route('home') }}" class="btn btn-outline-primary btn-lg rounded-pill px-5">
                    {{ __('Retour à l\'accueil') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
