@extends('layouts.dashboard')

@section('title', __('Paramètres de Paiement'))
@section('header', __('Paramètres de Paiement'))

@php
    $paymentService = app(\App\Services\PaymentService::class);
    $config         = $paymentService->getGlobalConfigurations();
    $countries      = $config['countries'];
    $allMethods     = $config['methods'];

    // Regrouper les méthodes par provider pour les filtres
    $providers = collect($allMethods)->pluck('provider')->unique()->sort()->values();
    $groups    = collect($allMethods)->pluck('group')->unique()->sort()->values();
@endphp

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1e293b;">⚙️ Modes de paiement</h4>
            <p class="text-muted small mb-0">Activez ou désactivez chaque méthode par pays. Les changements s'appliquent immédiatement.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-select-all">
                <i class="fas fa-check-double me-1"></i> Tout sélectionner
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger" id="btn-clear-all">
                <i class="fas fa-times me-1"></i> Tout désélectionner
            </button>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
        <div class="card-body py-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">🔍 Rechercher un pays</label>
                    <input type="text" id="filter-country" class="form-control form-control-sm" placeholder="Ex : Côte d'Ivoire, Bénin...">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">🏦 Prestataire</label>
                    <select id="filter-provider" class="form-select form-select-sm">
                        <option value="">Tous les prestataires</option>
                        @foreach($providers as $prov)
                            <option value="{{ $prov }}">{{ ucfirst($prov) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">📱 Type</label>
                    <select id="filter-group" class="form-select form-select-sm">
                        <option value="">Tous les types</option>
                        @foreach($groups as $g)
                            <option value="{{ $g }}">{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-light w-100" id="btn-reset-filters">
                        <i class="fas fa-undo me-1"></i> Réinitialiser
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulaire --}}
    <form action="{{ route('admin.settings.payment.update') }}" method="POST" id="payment-form">
        @csrf

        <div id="countries-container">
            @foreach($countries as $iso => $country)
            @php
                // Méthodes disponibles pour ce pays
                $countryMethods = collect($allMethods)->filter(fn($m) => in_array($iso, $m['countries']));
            @endphp
            @if($countryMethods->isEmpty()) @continue @endif

            <div class="card border-0 shadow-sm mb-3 country-card"
                 style="border-radius:12px; overflow:hidden;"
                 data-country-name="{{ strtolower($country['name']) }}"
                 data-country-iso="{{ $iso }}">

                {{-- Header pays --}}
                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between"
                     style="border-bottom: 2px solid #f1f5f9; cursor:pointer;"
                     data-bs-toggle="collapse"
                     data-bs-target="#country-{{ $iso }}">

                    <div class="d-flex align-items-center gap-3">
                        <div class="country-flag fw-bold text-white d-flex align-items-center justify-content-center rounded-circle"
                             style="width:42px;height:42px;background:linear-gradient(135deg,#3b82f6,#6366f1);font-size:.8rem;flex-shrink:0;">
                            {{ $iso }}
                        </div>
                        <div>
                            <div class="fw-semibold" style="color:#1e293b;">{{ $country['name'] }}</div>
                            <div class="text-muted" style="font-size:.75rem;">
                                {{ $country['currency'] }} &bull;
                                <span class="active-count text-success fw-semibold" id="count-{{ $iso }}">0</span> activé(s) / {{ $countryMethods->count() }}
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <button type="button" class="btn btn-xs btn-outline-success btn-select-country px-2 py-1"
                                style="font-size:.72rem;" data-iso="{{ $iso }}"
                                onclick="event.stopPropagation(); selectCountry('{{ $iso }}', true)">
                            ✓ Tout
                        </button>
                        <button type="button" class="btn btn-xs btn-outline-danger btn-clear-country px-2 py-1"
                                style="font-size:.72rem;" data-iso="{{ $iso }}"
                                onclick="event.stopPropagation(); selectCountry('{{ $iso }}', false)">
                            ✕ Rien
                        </button>
                        <i class="fas fa-chevron-down text-muted collapse-icon" style="font-size:.8rem;transition:.2s;"></i>
                    </div>
                </div>

                {{-- Méthodes --}}
                <div class="collapse show" id="country-{{ $iso }}">
                    <div class="card-body px-4 py-3">
                        <div class="row g-2">
                            @foreach($countryMethods as $methodCode => $method)
                            @php
                                $isChecked = isset($payment_methods[$iso][$methodCode]) && $payment_methods[$iso][$methodCode] === 'on';
                                $providerColors = [
                                    'touchpay'    => '#10b981',
                                    'paiementpro' => '#f59e0b',
                                    'pawapay'     => '#6366f1',
                                    'paystack'    => '#3b82f6',
                                    'wave'        => '#009FE3',
                                ];
                                $provColor = $providerColors[$method['provider']] ?? '#94a3b8';
                            @endphp
                            <div class="col-6 col-md-4 col-lg-3 method-item"
                                 data-provider="{{ $method['provider'] }}"
                                 data-group="{{ $method['group'] }}">
                                <label class="method-card d-flex align-items-center gap-2 p-2 rounded-3 w-100 {{ $isChecked ? 'active' : '' }}"
                                       for="check_{{ $iso }}_{{ $methodCode }}"
                                       style="cursor:pointer; border: 1.5px solid {{ $isChecked ? $method['icon_color'] : '#e2e8f0' }}; background: {{ $isChecked ? 'rgba('.implode(',',sscanf(ltrim($method['icon_color'],'#'),'%02x%02x%02x')).',0.06)' : '#fff' }}; transition: all .15s;">
                                    <input class="form-check-input mt-0 flex-shrink-0 method-checkbox"
                                           type="checkbox"
                                           name="methods[{{ $iso }}][{{ $methodCode }}]"
                                           id="check_{{ $iso }}_{{ $methodCode }}"
                                           data-iso="{{ $iso }}"
                                           {{ $isChecked ? 'checked' : '' }}
                                           style="width:16px;height:16px;accent-color:{{ $method['icon_color'] }};">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="fw-semibold text-truncate" style="font-size:.78rem;color:{{ $method['icon_color'] }};">
                                            {{ $method['name'] }}
                                        </div>
                                        <div class="d-flex align-items-center gap-1 mt-0" style="font-size:.65rem;color:#94a3b8;">
                                            <span class="rounded-pill px-1" style="background:{{ $provColor }}20;color:{{ $provColor }};font-weight:600;">
                                                {{ $method['provider'] }}
                                            </span>
                                            <span class="text-muted">{{ $methodCode }}</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Aucun résultat --}}
        <div id="no-results" class="text-center py-5 d-none">
            <i class="fas fa-search fa-2x text-muted mb-3 d-block"></i>
            <p class="text-muted">Aucun pays trouvé pour ces filtres.</p>
        </div>

        {{-- Footer sticky --}}
        <div class="position-sticky bottom-0 bg-white border-top shadow-sm py-3 px-4 d-flex align-items-center justify-content-between"
             style="border-radius:0 0 12px 12px; z-index:100;">
            <div class="text-muted small">
                <span id="total-checked">0</span> méthode(s) activée(s) au total
            </div>
            <button type="submit" class="btn btn-primary px-5 fw-semibold">
                <i class="fas fa-save me-2"></i> Enregistrer les modifications
            </button>
        </div>
    </form>
</div>

<style>
.method-card:hover {
    border-color: #94a3b8 !important;
    background: #f8fafc !important;
}
.method-card.active {
    box-shadow: 0 0 0 1px currentColor;
}
.collapse-icon { transition: transform .2s; }
.collapsed .collapse-icon { transform: rotate(-90deg); }
[data-bs-toggle="collapse"].collapsed .collapse-icon { transform: rotate(-90deg); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = () => document.querySelectorAll('.method-checkbox');
    const totalCheckedEl = document.getElementById('total-checked');

    // Compteur total
    function updateTotalCount() {
        totalCheckedEl.textContent = document.querySelectorAll('.method-checkbox:checked').length;
    }

    // Compteur par pays
    function updateCountryCount(iso) {
        const checked = document.querySelectorAll(`.method-checkbox[data-iso="${iso}"]:checked`).length;
        const el = document.getElementById('count-' + iso);
        if (el) el.textContent = checked;
    }

    // Initialisation des compteurs
    document.querySelectorAll('.country-card').forEach(card => {
        const iso = card.dataset.countryIso;
        updateCountryCount(iso);
    });
    updateTotalCount();

    // Style dynamique sur checkbox change
    document.querySelectorAll('.method-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            const label = this.closest('label');
            const color = this.style.accentColor;
            if (this.checked) {
                label.style.borderColor = color;
                label.style.background = 'rgba(0,0,0,0.03)';
            } else {
                label.style.borderColor = '#e2e8f0';
                label.style.background = '#fff';
            }
            updateCountryCount(this.dataset.iso);
            updateTotalCount();
        });
    });

    // Tout sélectionner / désélectionner
    document.getElementById('btn-select-all').addEventListener('click', () => {
        document.querySelectorAll('.method-item:not(.d-none) .method-checkbox').forEach(cb => {
            cb.checked = true; cb.dispatchEvent(new Event('change'));
        });
    });
    document.getElementById('btn-clear-all').addEventListener('click', () => {
        document.querySelectorAll('.method-item:not(.d-none) .method-checkbox').forEach(cb => {
            cb.checked = false; cb.dispatchEvent(new Event('change'));
        });
    });

    // Sélection par pays
    window.selectCountry = function(iso, state) {
        document.querySelectorAll(`.method-checkbox[data-iso="${iso}"]`).forEach(cb => {
            cb.checked = state; cb.dispatchEvent(new Event('change'));
        });
    };

    // ── Filtres ──────────────────────────────────────────────────────────────
    const filterCountry  = document.getElementById('filter-country');
    const filterProvider = document.getElementById('filter-provider');
    const filterGroup    = document.getElementById('filter-group');
    const noResults      = document.getElementById('no-results');

    function applyFilters() {
        const q        = filterCountry.value.toLowerCase().trim();
        const provider = filterProvider.value;
        const group    = filterGroup.value;
        let visible    = 0;

        document.querySelectorAll('.country-card').forEach(card => {
            const countryName = card.dataset.countryName;
            const countryIso  = card.dataset.countryIso;

            const matchName = !q || countryName.includes(q) || countryIso.toLowerCase().includes(q);

            // Filtrer les méthodes
            let anyMethodVisible = false;
            card.querySelectorAll('.method-item').forEach(item => {
                const matchProv  = !provider || item.dataset.provider === provider;
                const matchGroup = !group    || item.dataset.group    === group;
                const show       = matchProv && matchGroup;
                item.classList.toggle('d-none', !show);
                if (show) anyMethodVisible = true;
            });

            const show = matchName && anyMethodVisible;
            card.classList.toggle('d-none', !show);
            if (show) visible++;
        });

        noResults.classList.toggle('d-none', visible > 0);
    }

    filterCountry.addEventListener('input', applyFilters);
    filterProvider.addEventListener('change', applyFilters);
    filterGroup.addEventListener('change', applyFilters);

    document.getElementById('btn-reset-filters').addEventListener('click', () => {
        filterCountry.value = '';
        filterProvider.value = '';
        filterGroup.value = '';
        applyFilters();
    });

    // Icône chevron au collapse
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(btn => {
        const target = document.querySelector(btn.dataset.bsTarget);
        if (!target) return;
        target.addEventListener('hide.bs.collapse', () => btn.classList.add('collapsed'));
        target.addEventListener('show.bs.collapse', () => btn.classList.remove('collapsed'));
    });
});
</script>
@endsection