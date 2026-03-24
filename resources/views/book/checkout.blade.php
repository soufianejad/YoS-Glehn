@extends('layouts.app')

@section('title', __('Paiement') . ' - ' . $book->title)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow border-0 rounded-4 overflow-hidden">
                <div class="row g-0">
                    <!-- Left: Book Info -->
                    <div class="col-lg-5 bg-light p-4 p-md-5">
                        <div class="text-center mb-4">
                            <img src="{{ $book->cover_image ? asset('storage/'.$book->cover_image) : asset('images/default-book-cover.jpg') }}" 
                                 alt="{{ $book->title }}" class="img-fluid rounded-3 shadow-sm mb-3" style="max-height: 300px;">
                            <h3 class="fw-bold">{{ $book->title }}</h3>
                            <p class="text-muted">{{ $book->author }}</p>
                        </div>
                        
                        <div class="divider mb-4"></div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Type d\'achat') }}</span>
                            <span class="fw-bold text-uppercase">{{ $type }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fs-5">{{ __('Total à payer') }}</span>
                            <span class="fs-5 fw-bold text-primary">{{ number_format($price, 0) }} XOF</span>
                        </div>

                        <div class="alert alert-info border-0 rounded-3 small">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('Votre accès sera activé immédiatement après la confirmation du paiement.') }}
                        </div>
                    </div>

                    <!-- Right: Payment Form -->
                    <div class="col-lg-7 p-4 p-md-5">
                        <h4 class="fw-bold mb-4">{{ __('Choisir votre mode de paiement') }}</h4>
                        
                        <form action="{{ route($type === 'pdf' ? 'purchase.pdf' : 'purchase.audio', $book) }}" method="POST" id="payment-form">
                            @csrf
                            <input type="hidden" name="network" id="selected-network" required>
                            
                            <!-- Phone Number -->
                            <div class="mb-4">
                                <label for="phone" class="form-label fw-medium">{{ __('Numéro de téléphone') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-phone-alt text-muted"></i></span>
                                    <input type="tel" name="phone" id="phone" class="form-control border-start-0 ps-0" 
                                           placeholder="Ex: 0707070707" required value="{{ auth()->user()->phone }}">
                                </div>
                            </div>

                            <!-- Country Select -->
                            <div class="mb-4">
                                <label class="form-label fw-medium">{{ __('Pays') }}</label>
                                <select class="form-select" id="country-select">
                                    <option value="CI">Côte d'Ivoire</option>
                                    <option value="SN">Sénégal</option>
                                    <option value="BF">Burkina Faso</option>
                                    <option value="ML">Mali</option>
                                    <option value="BJ">Bénin</option>
                                    <option value="CM">Cameroun</option>
                                    <option value="TG">Togo</option>
                                    <option value="FR">France / Europe</option>
                                </select>
                            </div>

                            <!-- Networks Grid -->
                            <div class="mb-4">
                                <label class="form-label fw-medium">{{ __('Opérateur') }}</label>
                                <div class="row g-3" id="networks-grid">
                                    <!-- Dynamic content via JS -->
                                </div>
                            </div>

                            <!-- OTP Field (Hidden by default) -->
                            <div class="mb-4 d-none" id="otp-container">
                                <label for="otp" class="form-label fw-medium">{{ __('Code OTP Orange Money') }}</label>
                                <input type="text" name="otp" id="otp" class="form-control" placeholder="Composez le #144*82#">
                                <div class="form-text mt-2">
                                    {{ __('Générez votre code secret en composant le #144*82# sur votre mobile.') }}
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 py-3 shadow-sm fw-bold mt-2">
                                {{ __('Confirmer le paiement') }}
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <img src="https://www.paiementpro.net/images/logo.png" alt="PaiementPro" height="30" class="opacity-50 me-3">
                            <img src="https://api.gutouch.com/img/touchpay.png" alt="TouchPay" height="25" class="opacity-50">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .network-card {
        cursor: pointer;
        border: 2px solid #f0f0f0;
        transition: all 0.2s;
    }
    .network-card:hover {
        border-color: #dee2e6;
        background-color: #f8f9fa;
    }
    .network-card.selected {
        border-color: var(--bs-primary);
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }
    .network-icon {
        height: 40px;
        object-fit: contain;
    }
</style>
@endpush

@push('scripts')
<script>
    let currentNetworks = @json($methods['methods']);
    const networksGrid = document.getElementById('networks-grid');
    const networkInput = document.getElementById('selected-network');
    const otpContainer = document.getElementById('otp-container');
    const countrySelect = document.getElementById('country-select');
    const paymentForm = document.getElementById('payment-form');
    const submitBtn = paymentForm.querySelector('button[type="submit"]');

    function renderNetworks(methods) {
        networksGrid.innerHTML = '';
        if (methods.length === 0) {
            networksGrid.innerHTML = '<div class="col-12 text-center text-muted py-3 small">{{ __("Aucun mode de paiement disponible pour ce pays.") }}</div>';
            submitBtn.disabled = true;
            return;
        }
        
        submitBtn.disabled = false;
        methods.forEach(method => {
            const col = document.createElement('div');
            col.className = 'col-6 col-sm-4';
            col.innerHTML = `
                <div class="card network-card h-100 rounded-3 p-2 d-flex align-items-center justify-content-center text-center shadow-sm" data-id="${method.id}">
                    <div class="fw-bold small text-uppercase" style="color: ${method.icon_color}">${method.name}</div>
                </div>
            `;
            col.querySelector('.network-card').onclick = function() {
                selectNetwork(this);
            };
            networksGrid.appendChild(col);
        });

        // Auto-select first one
        if (methods.length > 0) {
            selectNetwork(networksGrid.querySelector('.network-card'));
        }
    }

    function selectNetwork(cardElement) {
        document.querySelectorAll('.network-card').forEach(c => c.classList.remove('selected', 'border-primary'));
        cardElement.classList.add('selected', 'border-primary');
        networkInput.value = cardElement.dataset.id;
        
        // Show OTP for Orange Money CI
        if (cardElement.dataset.id === 'ORANGE_CIV') {
            otpContainer.classList.remove('d-none');
        } else {
            otpContainer.classList.add('d-none');
        }
    }

    async function fetchNetworks(country) {
        networksGrid.innerHTML = '<div class="col-12 text-center py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>';
        try {
            const response = await fetch(`{{ route('payment.methods') }}?country=${country}`);
            const data = await response.json();
            currentNetworks = data.methods;
            renderNetworks(currentNetworks);
        } catch (error) {
            console.error('Error fetching networks:', error);
            networksGrid.innerHTML = '<div class="col-12 text-center text-danger py-3 small">{{ __("Erreur lors du chargement des modes de paiement.") }}</div>';
        }
    }

    countrySelect.addEventListener('change', (e) => {
        fetchNetworks(e.target.value);
    });

    paymentForm.addEventListener('submit', function(e) {
        if (!networkInput.value) {
            e.preventDefault();
            alert('{{ __("Veuillez choisir un opérateur.") }}');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> {{ __("Traitement en cours...") }}';
    });

    // Initial render
    renderNetworks(currentNetworks);
</script>
@endpush
@endsection
