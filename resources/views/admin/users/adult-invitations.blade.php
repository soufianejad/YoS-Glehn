@extends('layouts.dashboard')

@section('title', __('Gestion des Accès Adultes'))
@section('header', __('Gestion des Accès Adultes'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Générer une nouvelle invitation -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-lg mb-4">
                <div class="card-header bg-primary text-white p-3">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>{{ __('Générer une Invitation') }}</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.users.adult-invitation.generate') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">{{ __('Email (Optionnel)') }}</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="exemple@email.com">
                            <small class="text-muted">{{ __('Si spécifié, seul cet email pourra utiliser le token.') }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="max_uses" class="form-label fw-bold">{{ __('Nombre d\'utilisations') }}</label>
                            <input type="number" name="max_uses" id="max_uses" class="form-control" value="1" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label for="expires_at" class="form-label fw-bold">{{ __('Date d\'expiration') }}</label>
                            <input type="date" name="expires_at" id="expires_at" class="form-control">
                            <small class="text-muted">{{ __('Laissez vide pour une validité illimitée.') }}</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill shadow-sm">
                                <i class="fas fa-magic me-2"></i>{{ __('Générer le Token') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Liste des invitations -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white p-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary font-weight-bold"><i class="fas fa-list me-2"></i>{{ __('Invitations & Accès') }}</h5>
                    <span class="badge bg-light text-primary">{{ $invitations->total() }} total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">{{ __('Token / Email') }}</th>
                                    <th>{{ __('Statut') }}</th>
                                    <th>{{ __('Utilisation') }}</th>
                                    <th>{{ __('Expire le') }}</th>
                                    <th class="text-end pe-4">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invitations as $invitation)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex flex-column">
                                                <code class="text-primary fw-bold mb-1">{{ Str::limit($invitation->access_token, 10) }}...</code>
                                                <span class="small text-muted">{{ $invitation->email ?? __('Tout email') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = [
                                                    'pending' => 'bg-info',
                                                    'used' => 'bg-success',
                                                    'expired' => 'bg-danger',
                                                    'revoked' => 'bg-secondary'
                                                ][$invitation->status] ?? 'bg-light text-dark';
                                            @endphp
                                            <span class="badge {{ $statusClass }} rounded-pill px-3">
                                                {{ ucfirst(__($invitation->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 6px; width: 80px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($invitation->uses_count / $invitation->max_uses) * 100 }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ $invitation->uses_count }} / {{ $invitation->max_uses }}</small>
                                        </td>
                                        <td>
                                            @if($invitation->expires_at)
                                                <span class="{{ $invitation->isExpired() ? 'text-danger fw-bold' : '' }}">
                                                    {{ $invitation->expires_at->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="badge bg-light text-dark border">{{ __('Illimité') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                                <button class="btn btn-sm btn-light copy-token" data-token="{{ $invitation->access_token }}" title="{{ __('Copier le token') }}">
                                                    <i class="fas fa-copy text-primary"></i>
                                                </button>
                                                <a href="{{ route('admin.users.adult-invitation.edit', $invitation->access_token) }}" class="btn btn-sm btn-light" title="{{ __('Modifier') }}">
                                                    <i class="fas fa-edit text-warning"></i>
                                                </a>
                                                @if($invitation->status !== 'revoked')
                                                    <form action="{{ route('admin.users.adult-invitation.revoke', $invitation->access_token) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Révoquer cet accès ?') }}')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-light" title="{{ __('Révoquer') }}">
                                                            <i class="fas fa-ban text-danger"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <p class="text-muted mb-0">{{ __('Aucune invitation générée.') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($invitations->hasPages())
                    <div class="card-footer bg-white border-0 py-3">
                        {{ $invitations->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('.copy-token').click(function() {
            let token = $(this).data('token');
            // On utilise un placeholder pour générer l'URL proprement avec Laravel
            let baseUrl = "{{ route('adult.invitation', ['token' => 'TOKEN_PLACEHOLDER']) }}";
            let fullUrl = baseUrl.replace('TOKEN_PLACEHOLDER', token);
            
            navigator.clipboard.writeText(fullUrl).then(function() {
                toastr.success("{{ __('Lien d\'invitation copié !') }}");
            });
        });
    });
</script>
@endpush
@endsection
