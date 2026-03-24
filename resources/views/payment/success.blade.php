@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow border-0 rounded-4 p-5">
                <div class="mb-4 text-success">
                    <i class="fas fa-check-circle fa-5x"></i>
                </div>
                <h2 class="fw-bold mb-3">{{ __('Paiement réussi !') }}</h2>
                <p class="text-muted mb-4">{{ __('Votre achat a été validé avec succès. Vous pouvez maintenant accéder à votre contenu.') }}</p>
                <a href="{{ $url }}" class="btn btn-primary btn-lg rounded-pill px-5">
                    {{ __('Accéder au livre') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
