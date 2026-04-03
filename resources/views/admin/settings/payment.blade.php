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

    // The payment_methods config format: [iso => ['methodCode1', 'methodCode2', ...]] or [iso => ['methodCode1' => 'on', ...]]
    // Let's ensure it's handled uniformly.
@endphp

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<div class="container-fluid px-4 py-4">

    {{-- En-tête --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1" style="color:#1e293b;">⚙️ Modes de paiement</h4>
            <p class="text-muted small mb-0">Ajoutez des pays, activez, désactivez et réorganisez (drag & drop) les méthodes de paiement pour chaque pays.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCountryModal">
                <i class="fas fa-plus me-1"></i> Ajouter un pays
            </button>
        </div>
    </div>

    {{-- Modal Ajouter un pays --}}
    <div class="modal fade" id="addCountryModal" tabindex="-1" aria-labelledby="addCountryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.settings.payment.country.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCountryModalLabel">Ajouter un nouveau pays</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="country_name" class="form-label">Nom du pays</label>
                            <input type="text" class="form-control" id="country_name" name="name" required placeholder="ex: Sénégal">
                        </div>
                        <div class="mb-3">
                            <label for="country_iso" class="form-label">Code ISO (2 lettres)</label>
                            <input type="text" class="form-control" id="country_iso" name="iso" required placeholder="ex: SN" maxlength="2" style="text-transform: uppercase;">
                        </div>
                        <div class="mb-3">
                            <label for="country_currency" class="form-label">Devise</label>
                            <input type="text" class="form-control" id="country_currency" name="currency" required placeholder="ex: XOF" maxlength="3" style="text-transform: uppercase;">
                        </div>
                        <div class="mb-3">
                            <label for="country_code" class="form-label">Indicatif téléphonique</label>
                            <input type="text" class="form-control" id="country_code" name="code" placeholder="ex: 221">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Banque de Méthodes --}}
    <div class="card border-primary shadow-sm mb-4 bg-white" style="border-radius:12px; border-width: 2px;">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
            <h5 class="fw-bold text-primary mb-1"><i class="fas fa-university me-2"></i> Banque de Méthodes</h5>
            <p class="text-muted small mb-0">Glissez une méthode de paiement depuis cette zone vers un pays ci-dessous. Vous pouvez réutiliser les méthodes autant de fois que nécessaire.</p>
        </div>
        <div class="card-body">
            <div class="row g-2 methods-bank" id="global-methods-list" style="min-height: 80px;">
                @foreach($allMethods as $methodCode => $method)
                @php
                    $providerColors = [
                        'touchpay'    => '#10b981',
                        'paiementpro' => '#f59e0b',
                        'pawapay'     => '#6366f1',
                        'paystack'    => '#3b82f6',
                        'wave'        => '#009FE3',
                    ];
                    $provColor = $providerColors[$method['provider']] ?? '#94a3b8';
                @endphp
                <div class="col-6 col-md-4 col-lg-2 method-item global-method"
                     data-provider="{{ $method['provider'] }}"
                     data-group="{{ $method['group'] }}"
                     data-method-code="{{ $methodCode }}">
                    <div class="method-card d-flex align-items-center gap-2 p-2 rounded-3 w-100"
                           style="cursor:grab; border: 1.5px dashed #cbd5e1; background: #f8fafc; transition: all .15s;">
                        <div class="drag-handle-method text-muted" style="cursor: grab;">
                            <i class="fas fa-grip-vertical"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-semibold text-truncate" style="font-size:.78rem;color:{{ $method['icon_color'] }};">
                                {{ $method['name'] }}
                            </div>
                            <div class="d-flex align-items-center gap-1 mt-0" style="font-size:.65rem;color:#94a3b8;">
                                <span class="rounded-pill px-1" style="background:{{ $provColor }}20;color:{{ $provColor }};font-weight:600;">
                                    {{ $method['provider'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Formulaire --}}
    <form action="{{ route('admin.settings.payment.update') }}" method="POST" id="payment-form">
        @csrf

        <div id="countries-container" class="row">
            @foreach($countries as $iso => $country)
            @php
                // Toutes les méthodes sont désormais disponibles
                $countryMethods = collect($allMethods);

                // Déterminer l'ordre des méthodes à partir des paramètres
                $savedCountryMethods = isset($payment_methods[$iso]) ? $payment_methods[$iso] : [];
                $orderedKeys = is_array($savedCountryMethods) && count($savedCountryMethods) > 0 && is_numeric(key($savedCountryMethods))
                    ? $savedCountryMethods
                    : array_keys(array_filter($savedCountryMethods, fn($v) => $v === 'on'));

                $countryMethods = $countryMethods->sortBy(function($method, $methodCode) use ($orderedKeys) {
                    $pos = array_search($methodCode, $orderedKeys);
                    return $pos !== false ? $pos : 9999;
                });
            @endphp

            <div class="col-md-6 mb-4">
            <div class="card border shadow-sm country-card h-100"
                 style="border-radius:12px; border-color:#e2e8f0;"
                 data-country-name="{{ strtolower($country['name']) }}"
                 data-country-iso="{{ $iso }}">
                <input type="hidden" name="countries_order[]" value="{{ $iso }}">

                {{-- Header pays --}}
                <div class="card-header bg-light border-0 py-3 px-4 d-flex align-items-center justify-content-between"
                     style="border-bottom: 2px solid #f1f5f9; border-radius:12px 12px 0 0;">

                    <div class="d-flex align-items-center gap-3">
                        <div class="drag-handle-country text-muted me-2" style="cursor: grab;">
                            <i class="fas fa-grip-vertical"></i>
                        </div>
                        <div class="country-flag fw-bold text-white d-flex align-items-center justify-content-center rounded-circle"
                             style="width:42px;height:42px;background:linear-gradient(135deg,#3b82f6,#6366f1);font-size:.8rem;flex-shrink:0;">
                            {{ $iso }}
                        </div>
                        <div>
                            <div class="fw-semibold" style="color:#1e293b;">{{ $country['name'] }}</div>
                            <div class="text-muted" style="font-size:.75rem;">
                                {{ $country['currency'] }} &bull;
                                <span class="active-count text-success fw-semibold" id="count-{{ $iso }}">0</span> activé(s)
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:.72rem;">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow" style="font-size:.8rem; max-height:300px; overflow-y:auto; z-index:1050;">
                                @foreach($allMethods as $mc => $m)
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="#" onclick="event.preventDefault(); addMethodToCountry('{{ $iso }}', '{{ $mc }}')">
                                            <div style="width:12px;height:12px;background:{{ $m['icon_color'] }};border-radius:50%;"></div>
                                            {{ $m['name'] }} <small class="text-muted ms-auto">{{ $m['provider'] }}</small>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-clear-country px-2 py-1"
                                style="font-size:.72rem;" data-iso="{{ $iso }}"
                                onclick="clearCountry('{{ $iso }}')">
                            <i class="fas fa-trash"></i> Vider
                        </button>
                    </div>
                </div>

                {{-- Méthodes --}}
                <div class="card-body px-3 py-3" style="background:#f8fafc; min-height:120px; border-radius:0 0 12px 12px;">
                    <div class="row g-2 methods-sortable" data-iso="{{ $iso }}" style="min-height:100%;">
                        @foreach($countryMethods as $methodCode => $method)
                        @php
                            // Ne montrer que les méthodes qui étaient effectivement activées
                            $isChecked = isset($payment_methods[$iso][$methodCode]) && $payment_methods[$iso][$methodCode] === 'on';
                            if (!$isChecked) continue;

                            $providerColors = [
                                'touchpay'    => '#10b981',
                                'paiementpro' => '#f59e0b',
                                'pawapay'     => '#6366f1',
                                'paystack'    => '#3b82f6',
                                'wave'        => '#009FE3',
                            ];
                            $provColor = $providerColors[$method['provider']] ?? '#94a3b8';
                        @endphp
                        <div class="col-12 col-md-6 col-lg-4 method-item"
                             data-provider="{{ $method['provider'] }}"
                             data-group="{{ $method['group'] }}"
                             data-method-code="{{ $methodCode }}">
                            <input type="hidden" name="methods_order[{{ $iso }}][]" value="{{ $methodCode }}">
                            <input type="hidden" name="methods[{{ $iso }}][{{ $methodCode }}]" value="on">
                            <div class="method-card d-flex align-items-center gap-2 p-2 rounded-3 w-100"
                                   style="cursor:grab; border: 1.5px solid {{ $method['icon_color'] }}; background: #fff; transition: all .15s;">
                                <div class="drag-handle-method text-muted" style="cursor: grab;">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-semibold text-truncate" style="font-size:.78rem;color:{{ $method['icon_color'] }};">
                                        {{ $method['name'] }}
                                    </div>
                                    <div class="d-flex align-items-center gap-1 mt-0" style="font-size:.65rem;color:#94a3b8;">
                                        <span class="rounded-pill px-1" style="background:{{ $provColor }}20;color:{{ $provColor }};font-weight:600;">
                                            {{ $method['provider'] }}
                                        </span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm text-danger border-0 p-1 remove-method" onclick="this.closest('.method-item').remove(); updateTotalCount(); updateCountryCount('{{ $iso }}');">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
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
.sortable-ghost { opacity: 0.4; }
.sortable-drag { cursor: grabbing !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Sortable for Countries
    const countriesContainer = document.getElementById('countries-container');
    if (countriesContainer) {
        new Sortable(countriesContainer, {
            handle: '.drag-handle-country',
            animation: 150,
            ghostClass: 'sortable-ghost',
        });
    }

    // Initialize Sortable for the Global Bank
    const globalBank = document.getElementById('global-methods-list');
    if (globalBank) {
        new Sortable(globalBank, {
            group: {
                name: 'shared-methods',
                pull: 'clone', // Clone the element when dragging out
                put: false     // Do not allow dragging elements back into the bank
            },
            animation: 150,
            sort: false, // Prevent sorting within the bank
            ghostClass: 'sortable-ghost',
        });
    }

    // Initialize Sortable for Methods inside each Country
    document.querySelectorAll('.methods-sortable').forEach(container => {
        new Sortable(container, {
            group: 'shared-methods',
            handle: '.drag-handle-method',
            animation: 150,
            ghostClass: 'sortable-ghost',
            onAdd: function (evt) {
                const itemEl = evt.item; // The cloned item element
                const iso = container.dataset.iso;
                const methodCode = itemEl.dataset.methodCode;

                // Check for duplicates
                const existingItems = container.querySelectorAll(`[data-method-code="${methodCode}"]`);
                if (existingItems.length > 1) {
                    itemEl.remove(); // Remove duplicate
                    alert('Cette méthode est déjà activée pour ce pays.');
                    return;
                }

                // Update classes for layout
                itemEl.className = 'col-12 col-md-6 col-lg-4 method-item';

                // Ensure solid border and correct background after drop
                const card = itemEl.querySelector('.method-card');
                if(card) {
                    card.style.borderStyle = 'solid';
                }

                // Remove old hidden inputs if this item was dragged from another country
                const oldInputs = itemEl.querySelectorAll('input[type="hidden"]');
                oldInputs.forEach(input => input.remove());

                // Add hidden inputs for ordering and state for the NEW country
                const inputsHtml = `
                    <input type="hidden" name="methods_order[${iso}][]" value="${methodCode}">
                    <input type="hidden" name="methods[${iso}][${methodCode}]" value="on">
                `;
                itemEl.insertAdjacentHTML('afterbegin', inputsHtml);

                // Add delete button
                if (!itemEl.querySelector('.remove-method')) {
                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.className = 'btn btn-sm text-danger border-0 p-1 remove-method';
                    deleteBtn.innerHTML = '<i class="fas fa-times"></i>';
                    deleteBtn.onclick = function() {
                        itemEl.remove();
                        updateTotalCount();
                        updateCountryCount(iso);
                    };
                    if(card) {
                        card.appendChild(deleteBtn);
                    } else {
                        itemEl.appendChild(deleteBtn);
                    }
                }

                updateTotalCount();
                updateCountryCount(iso);
            }
        });
    });

    const totalCheckedEl = document.getElementById('total-checked');

    // Compteur total
    function updateTotalCount() {
        if (totalCheckedEl) {
            totalCheckedEl.textContent = document.querySelectorAll('#countries-container .method-item').length;
        }
    }

    // Compteur par pays
    window.updateCountryCount = function(iso) {
        const checked = document.querySelectorAll(`.methods-sortable[data-iso="${iso}"] .method-item`).length;
        const el = document.getElementById('count-' + iso);
        if (el) el.textContent = checked;
    }

    // Initialisation des compteurs
    document.querySelectorAll('.country-card').forEach(card => {
        const iso = card.dataset.countryIso;
        updateCountryCount(iso);
    });
    updateTotalCount();

    // Vider un pays
    window.clearCountry = function(iso) {
        if(confirm('Voulez-vous vraiment retirer toutes les méthodes de ce pays ?')) {
            const container = document.querySelector(`.methods-sortable[data-iso="${iso}"]`);
            if (container) {
                container.innerHTML = '';
                updateCountryCount(iso);
                updateTotalCount();
            }
        }
    };

    // Ajouter manuellement une méthode via dropdown
    window.addMethodToCountry = function(iso, methodCode) {
        const container = document.querySelector(`.methods-sortable[data-iso="${iso}"]`);

        // Vérifier si la méthode n'est pas déjà ajoutée
        const existingItems = container.querySelectorAll(`[data-method-code="${methodCode}"]`);
        if (existingItems.length > 0) {
            alert('Cette méthode est déjà activée pour ce pays.');
            return;
        }

        // Trouver le clone source dans la banque de méthodes
        const sourceItem = document.querySelector(`#global-methods-list [data-method-code="${methodCode}"]`);
        if (!sourceItem) return;

        // Cloner et adapter
        const itemEl = sourceItem.cloneNode(true);
        itemEl.className = 'col-12 col-md-6 col-lg-4 method-item';

        const card = itemEl.querySelector('.method-card');
        if (card) card.style.borderStyle = 'solid';

        // Supprimer d'éventuels anciens inputs (si clone d'ailleurs)
        const oldInputs = itemEl.querySelectorAll('input[type="hidden"]');
        oldInputs.forEach(input => input.remove());

        // Ajouter les bons inputs
        const inputsHtml = `
            <input type="hidden" name="methods_order[${iso}][]" value="${methodCode}">
            <input type="hidden" name="methods[${iso}][${methodCode}]" value="on">
        `;
        itemEl.insertAdjacentHTML('afterbegin', inputsHtml);

        // Ajouter bouton delete
        if (!itemEl.querySelector('.remove-method')) {
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'btn btn-sm text-danger border-0 p-1 remove-method';
            deleteBtn.innerHTML = '<i class="fas fa-times"></i>';
            deleteBtn.onclick = function() {
                itemEl.remove();
                updateTotalCount();
                updateCountryCount(iso);
            };
            if(card) card.appendChild(deleteBtn);
            else itemEl.appendChild(deleteBtn);
        }

        // Ajouter dans le container
        container.appendChild(itemEl);

        // Mettre à jour les compteurs
        updateTotalCount();
        updateCountryCount(iso);
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