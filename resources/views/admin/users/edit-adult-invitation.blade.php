@extends('layouts.dashboard')

@section('title', __('Modifier l\'accès adulte'))
@section('header', __('Modifier l\'accès adulte'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white p-3">
                    <h5 class="mb-0 text-primary font-weight-bold">
                        <i class="fas fa-edit me-2"></i>{{ __('Détails de l\'accès') }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.users.adult-invitation.update', $invitation->access_token) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('Token') }}</label>
                            <input type="text" class="form-control bg-light" value="{{ $invitation->access_token }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">{{ __('Email restreint') }}</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ $invitation->email }}" placeholder="exemple@email.com">
                            <small class="text-muted">{{ __('Laissez vide pour autoriser n\'importe quel email lors de l\'inscription.') }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">{{ __('Statut') }}</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="pending" {{ $invitation->status == 'pending' ? 'selected' : '' }}>{{ __('En attente (Disponible)') }}</option>
                                <option value="used" {{ $invitation->status == 'used' ? 'selected' : '' }}>{{ __('Utilisé / Actif') }}</option>
                                <option value="expired" {{ $invitation->status == 'expired' ? 'selected' : '' }}>{{ __('Expiré') }}</option>
                                <option value="revoked" {{ $invitation->status == 'revoked' ? 'selected' : '' }}>{{ __('Révoqué') }}</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="max_uses" class="form-label fw-bold">{{ __('Max Utilisations') }}</label>
                                <input type="number" name="max_uses" id="max_uses" class="form-control" value="{{ $invitation->max_uses }}" min="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="uses_count" class="form-label fw-bold">{{ __('Utilisations actuelles') }}</label>
                                <input type="number" class="form-control bg-light" value="{{ $invitation->uses_count }}" readonly>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="expires_at" class="form-label fw-bold">{{ __('Date d\'expiration') }}</label>
                            <input type="date" name="expires_at" id="expires_at" class="form-control" value="{{ $invitation->expires_at ? $invitation->expires_at->format('Y-m-d') : '' }}">
                            <small class="text-muted">{{ __('Laissez vide pour une validité illimitée.') }}</small>
                        </div>

                        @if($invitation->user)
                            <div class="alert alert-info border-0 shadow-sm rounded-lg mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-check fa-2x me-3"></i>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ __('Utilisateur lié') }}</h6>
                                        <p class="mb-0 small">{{ $invitation->user->name }} ({{ $invitation->user->email }})</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.users.adult-invitations') }}" class="btn btn-light rounded-pill px-4">
                                <i class="fas fa-arrow-left me-2"></i>{{ __('Retour') }}
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="fas fa-save me-2"></i>{{ __('Enregistrer les modifications') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
