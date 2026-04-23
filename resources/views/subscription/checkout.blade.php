@extends('layouts.app')

@section('title', __('Paiement') . ' — ' . (isset($book) ? $book->title : $plan->name))

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
<style>
    .iti { width: 100%; }
    :root {
        --primary-color: #0d6efd;
        --bg-light: #f8fafc;
        --border-color: #e2e8f0;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --radius: 12px;
    }

    body {
        background-color: var(--bg-light);
        color: var(--text-dark);
    }

    .checkout-container {
        max-width: 1000px;
        margin: 40px auto;
    }

    .card {
        border: none;
        border-radius: var(--radius);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .summary-card {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        color: white;
    }

    .summary-card .text-muted {
        color: rgba(255, 255, 255, 0.7) !important;
    }

    .summary-card hr {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .cover-img {
        width: 100px;
        height: 140px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
    }

    .plan-icon-large {
        font-size: 3rem;
        background: rgba(255, 255, 255, 0.1);
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-bottom: 1rem;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        color: var(--text-muted);
    }

    .network-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 12px;
    }

    .network-card {
        border: 2px solid var(--border-color);
        border-radius: 10px;
        padding: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: white;
        text-align: center;
    }

    .network-card:hover {
        border-color: var(--primary-color);
        background-color: #f0f7ff;
    }

    .network-card.selected {
        border-color: var(--primary-color);
        background-color: #ebf5ff;
        box-shadow: 0 0 0 1px var(--primary-color);
    }

    .network-card .net-name {
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 4px;
    }

    .network-card .net-sub {
        font-size: 0.7rem;
        color: var(--text-muted);
        font-family: monospace;
    }

    .btn-pay {
        background: var(--primary-color);
        border: none;
        border-radius: 10px;
        padding: 14px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }

    .btn-pay:hover:not(:disabled) {
        background: #0b5ed7;
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(13, 110, 253, 0.3);
    }

    .btn-pay:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .provider-tag {
        font-size: 0.65rem;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .otp-box {
        background: #fffbeb;
        border: 1px solid #fef3c7;
        color: #92400e;
    }

    .steps-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
    }

    .step-item {
        text-align: center;
        flex: 1;
        position: relative;
    }

    .step-item:not(:last-child):after {
        content: '';
        position: absolute;
        top: 15px;
        left: 50%;
        width: 100%;
        height: 2px;
        background: var(--border-color);
        z-index: 1;
    }

    .step-number {
        width: 32px;
        height: 32px;
        background: white;
        border: 2px solid var(--border-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-weight: 700;
        position: relative;
        z-index: 2;
    }

    .step-item.active .step-number {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }

    .step-item.completed .step-number {
        background: #10b981;
        border-color: #10b981;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container checkout-container">
    <div class="row g-4">
        <!-- Colonne Gauche : Résumé -->
        <div class="col-lg-4 order-lg-2">
            <div class="card summary-card p-4">
                <h4 class="mb-4">{{ __('Résumé de la commande') }}</h4>
                
                <div class="d-flex align-items-center gap-3 mb-4">
                    @if(isset($book))
                        <img src="{{ $book->cover_image ? asset('storage/'.$book->cover_image) : asset('images/default-book-cover.jpg') }}" class="cover-img">
                        <div>
                            <h6 class="mb-1 fw-bold">{{ $book->title }}</h6>
                            <p class="small text-muted mb-0">{{ $book->author->name ?? __('Auteur') }}</p>
                        </div>
                    @elseif(isset($plan))
                        <div class="plan-icon-large">📚</div>
                        <div>
                            <h6 class="mb-1 fw-bold">{{ $plan->name }}</h6>
                            <p class="small text-muted mb-0">{{ $plan->duration_days }} {{ __('jours d\'accès') }}</p>
                        </div>
                    @endif
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">{{ __('Type') }}</span>
                    <span class="fw-bold small">
                        @if(isset($book)) {{ strtoupper($type) }} @else {{ $type === 'renewal' ? __('Renouvellement') : __('Nouvel abonnement') }} @endif
                    </span>
                </div>

                <div class="d-flex justify-content-between mb-4">
                    <span class="text-muted small">{{ __('Taxes incluses') }}</span>
                    <span class="fw-bold small">{{ formatPrice(0) }}</span>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="h5 mb-0 fw-bold text-muted">{{ __('Total') }}</span>
                    <span class="h3 mb-0 fw-bold">{{ formatPrice($price) }}</span>
                </div>

                <div class="mt-4 p-3 rounded bg-white bg-opacity-10 small">
                    <div class="mb-2"><i class="fas fa-lock me-2"></i> {{ __('Paiement 100% sécurisé') }}</div>
                    <div class="mb-2"><i class="fas fa-check-circle me-2"></i> {{ __('Accès immédiat après validation') }}</div>
                </div>
            </div>
        </div>

        <!-- Colonne Droite : Formulaire -->
        <div class="col-lg-8 order-lg-1">
            <div class="card p-4 p-md-5">
                <!-- Indicateur d'étapes -->
                <div class="steps-indicator">
                    <div class="step-item completed">
                        <div class="step-number"><i class="fas fa-check"></i></div>
                        <div class="small fw-bold text-muted">{{ __('Plan') }}</div>
                    </div>
                    <div class="step-item active">
                        <div class="step-number">2</div>
                        <div class="small fw-bold">{{ __('Paiement') }}</div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="small fw-bold text-muted">{{ __('Succès') }}</div>
                    </div>
                </div>

                <h3 class="fw-bold mb-4">{{ __('Méthode de paiement') }}</h3>

                @if(isset($book))
                    <form action="{{ route($type === 'pdf' ? 'purchase.pdf' : 'purchase.audio', $book) }}" method="POST" id="payment-form">
                @elseif(isset($plan))
                    <form action="{{ $type === 'renewal' ? route('subscription.renew') : route('subscription.subscribe', $plan) }}" method="POST" id="payment-form">
                @endif
                    @csrf
                    <input type="hidden" name="network" id="selected-network">

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label">{{ __('Votre numéro de téléphone') }}</label>
                            <div>
                                @php
                                    $userPhone = auth()->user()->phone ?? '';
                                    if ($userPhone && !str_starts_with($userPhone, '+')) {
                                        $userPhone = '+' . $userPhone;
                                    }
                                @endphp
                                <input type="tel" name="phone_display" id="phone" class="form-control bg-light"
                                       value="{{ $userPhone }}" readonly required>
                                <input type="hidden" name="phone" id="phone_hidden">
                                <input type="hidden" name="country_iso" id="country_iso">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label d-block mb-3">{{ __('Sélectionnez votre opérateur') }}</label>
                        <div id="networks-container">
                            <div class="text-center py-4 text-muted" id="networks-loading">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                {{ __('Chargement des opérateurs...') }}
                            </div>
                            <div id="networks-grid" class="d-none"></div>
                            <div id="networks-empty" class="d-none alert alert-warning small">
                                {{ __('Aucun opérateur disponible pour ce pays.') }}
                            </div>
                        </div>
                        <p id="network-error" class="text-danger small mt-2 d-none">
                            <i class="fas fa-exclamation-circle me-1"></i> {{ __('Veuillez sélectionner un opérateur.') }}
                        </p>
                    </div>

                    <!-- OTP Orange Money (CI) -->
                    <div class="otp-box p-3 rounded mb-4 d-none" id="otp-container">
                        <div class="d-flex gap-3 align-items-center">
                            <div class="h2 mb-0"><i class="fas fa-shield-alt"></i></div>
                            <div>
                                <div class="fw-bold">{{ __('Code OTP requis') }}</div>
                                <div class="small opacity-75">{{ __('Composez #144*82# pour obtenir votre code.') }}</div>
                            </div>
                        </div>
                        <input type="text" name="otp" id="otp" class="form-control mt-3" placeholder="Entrez le code reçu">
                    </div>

                    <button type="submit" class="btn btn-primary btn-pay w-100 text-white" id="submit-btn">
                        <span class="btn-text">{{ __('Confirmer et Payer') }} · {{ formatPrice($price) }}</span>
                        <span class="spinner-border spinner-border-sm d-none" id="btn-spinner"></span>
                    </button>

                    <div class="mt-4 d-flex justify-content-center gap-4 opacity-50 grayscale-img">
                        <img src="https://www.paiementpro.net/images/logo.png" height="20" alt="PaiementPro">
                        <img src="https://api.gutouch.com/img/touchpay.png" height="20" alt="TouchPay">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentMethods = @json($methods['methods'] ?? []);
    const gridEl = document.getElementById('networks-grid');
    const loadingEl = document.getElementById('networks-loading');
    const emptyEl = document.getElementById('networks-empty');
    const networkInput = document.getElementById('selected-network');
    const phoneInput = document.querySelector("#phone");
    const phoneHiddenInput = document.querySelector("#phone_hidden");
    const countryIsoInput = document.querySelector("#country_iso");
    const otpBox = document.getElementById('otp-container');
    const submitBtn = document.getElementById('submit-btn');

    const iti = window.intlTelInput(phoneInput, {
        initialCountry: "auto",
        allowDropdown: false,
        geoIpLookup: function(callback) {
            fetch("https://ipinfo.io/json")
                .then(resp => resp.json())
                .then(data => callback(data.country))
                .catch(() => callback("ci"));
        },
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        preferredCountries: ['ci', 'sn', 'bj', 'bf', 'ml', 'ne', 'tg', 'cm', 'fr', 'cd', 'ng', 'gh']
    });

    function renderNetworks(methods) {
        loadingEl.classList.add('d-none');
        gridEl.innerHTML = '';

        if (!methods || methods.length === 0) {
            emptyEl.classList.remove('d-none');
            gridEl.classList.add('d-none');
            return;
        }

        emptyEl.classList.add('d-none');
        gridEl.classList.remove('d-none');
        gridEl.className = 'network-grid';

        methods.forEach(method => {
            const card = document.createElement('div');
            card.className = 'network-card';
            card.dataset.id = method.id;
            card.innerHTML = `
                <div class="net-name" style="color:${method.icon_color}">${method.name}</div>
                <div class="provider-tag bg-light text-muted">${method.provider}</div>
            `;
            card.addEventListener('click', () => {
                document.querySelectorAll('.network-card').forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                networkInput.value = method.id;
                otpBox.classList.toggle('d-none', method.id !== 'OMCIV2');
            });
            gridEl.appendChild(card);
        });

        // Auto select first
        if (gridEl.firstChild) gridEl.firstChild.click();
    }

    async function fetchNetworks(country) {
        loadingEl.classList.remove('d-none');
        gridEl.classList.add('d-none');
        emptyEl.classList.add('d-none');

        try {
            const resp = await fetch(`{{ route('payment.methods') }}?country=${country}`);
            const data = await resp.json();
            renderNetworks(data.methods || []);
        } catch (err) {
            loadingEl.classList.add('d-none');
            emptyEl.classList.remove('d-none');
        }
    }

    phoneInput.addEventListener("countrychange", function() {
        const countryData = iti.getSelectedCountryData();
        const iso2 = (countryData.iso2 || 'ci').toUpperCase();
        countryIsoInput.value = iso2;
        fetchNetworks(iso2);
    });

    document.getElementById('payment-form').addEventListener('submit', function (e) {
        if (!networkInput.value) {
            e.preventDefault();
            document.getElementById('network-error').classList.remove('d-none');
            return;
        }
        // Update hidden inputs with full phone number and country
        if (iti.isValidNumber()) {
            phoneHiddenInput.value = iti.getNumber();
        } else {
            phoneHiddenInput.value = phoneInput.value;
        }

        submitBtn.disabled = true;
        document.getElementById('btn-spinner').classList.remove('d-none');
        document.querySelector('.btn-text').classList.add('d-none');
    });

    // Ensure country input is set initially
    phoneInput.addEventListener("iti:initialised", function() {
        const countryData = iti.getSelectedCountryData();
        const iso2 = (countryData.iso2 || 'ci').toUpperCase();
        countryIsoInput.value = iso2;
        // On load, we use the initial networks injected via Blade.
        // We could also fetch them based on detected IP:
        fetchNetworks(iso2);
    });

    // Fallback if not initialized fast enough
    renderNetworks(currentMethods);
});
</script>
@endpush
