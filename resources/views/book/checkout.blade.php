{{--
    Vue de checkout unifiée — utilisable pour :
      - Achat livre PDF/Audio  → variables : $book, $type (pdf|audio), $price, $methods
      - Abonnement             → variables : $plan, $type (subscription|renewal), $price, $methods, [$subscription]
    La variable $context (book|subscription) est injectée par le contrôleur pour adapter le formulaire.
--}}
@extends('layouts.app')

@section('title')
    {{ __('Paiement') }} —
    @if(isset($book)) {{ $book->title }}
    @elseif(isset($plan)) {{ $plan->name }}
    @endif
@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
/* ── Variables & Reset ───────────────────────────────────────────────── */
:root {
    --c-bg:      #f8fafc;
    --c-surface: #ffffff;
    --c-border:  #e2e8f0;
    --c-text:    #0f172a;
    --c-muted:   #64748b;
    --c-accent:  #4f46e5;
    --c-accent-light: #eef2ff;
    --c-success: #10b981;
    --c-danger:  #ef4444;
    --radius:    14px;
    --radius-sm: 8px;
    --shadow:    0 1px 3px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.05);
}
body { background: var(--c-bg); font-family: 'DM Sans', sans-serif; color: var(--c-text); }

/* ── Layout ─────────────────────────────────────────────────────────── */
.checkout-wrap { display:grid; grid-template-columns: 400px 1fr; min-height: 100vh; }
@media(max-width:900px){ .checkout-wrap{ grid-template-columns:1fr; } .panel-left{ display:none; } }

/* ── Panel gauche (résumé) ───────────────────────────────────────────── */
.panel-left {
    background: linear-gradient(160deg, #1e1b4b 0%, #312e81 100%);
    color:#fff;
    padding: 3rem 2.5rem;
    position: sticky;
    top: 0;
    height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow: hidden;
}
.panel-left::before {
    content:'';
    position:absolute;
    top:-80px; right:-80px;
    width:300px; height:300px;
    border-radius:50%;
    background: rgba(255,255,255,.04);
}
.panel-left::after {
    content:'';
    position:absolute;
    bottom:-60px; left:-60px;
    width:200px; height:200px;
    border-radius:50%;
    background: rgba(255,255,255,.03);
}
.cover-img {
    width: 140px; height: 200px;
    object-fit: cover;
    border-radius: 10px;
    box-shadow: 0 20px 40px rgba(0,0,0,.4);
    margin-bottom: 2rem;
    position: relative; z-index:1;
}
.plan-icon {
    width:100px; height:100px;
    border-radius: 50%;
    background: rgba(255,255,255,.12);
    backdrop-filter: blur(10px);
    display:flex; align-items:center; justify-content:center;
    font-size: 2.5rem;
    margin-bottom: 2rem;
    position: relative; z-index:1;
}
.summary-title { font-size:1.4rem; font-weight:700; margin-bottom:.5rem; position:relative;z-index:1; }
.summary-sub   { color:rgba(255,255,255,.65); font-size:.9rem; margin-bottom:2rem; position:relative;z-index:1; }
.summary-divider { border-color:rgba(255,255,255,.15); margin: 1.5rem 0; }
.summary-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:.75rem; position:relative;z-index:1; }
.summary-row .label { color:rgba(255,255,255,.6); font-size:.85rem; }
.summary-row .val   { font-weight:600; font-size:.95rem; }
.summary-total { position:relative;z-index:1; }
.summary-total .label { color:rgba(255,255,255,.7); font-size:.9rem; }
.summary-total .val {
    font-size:2rem; font-weight:700;
    background: linear-gradient(135deg,#a5b4fc,#818cf8);
    -webkit-background-clip:text; -webkit-text-fill-color:transparent;
}
.trust-badges { display:flex; gap:1rem; flex-wrap:wrap; margin-top:2.5rem; position:relative;z-index:1; }
.badge-item { display:flex; align-items:center; gap:.4rem; font-size:.72rem; color:rgba(255,255,255,.55); }
.badge-item i { color:rgba(255,255,255,.4); }

/* ── Panel droit (formulaire) ────────────────────────────────────────── */
.panel-right {
    padding: 3rem 2.5rem;
    max-width: 580px;
    width: 100%;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* ── Steps ───────────────────────────────────────────────────────────── */
.steps { display:flex; gap:.5rem; align-items:center; margin-bottom:2.5rem; }
.step { width:28px;height:28px; border-radius:50%; display:flex;align-items:center;justify-content:center; font-size:.75rem; font-weight:700; transition:.2s; }
.step.done   { background:var(--c-success); color:#fff; }
.step.active { background:var(--c-accent); color:#fff; box-shadow:0 0 0 3px var(--c-accent-light); }
.step.pending{ background:var(--c-border); color:var(--c-muted); }
.step-line { flex:1; height:2px; border-radius:1px; background:var(--c-border); }
.step-line.done { background:var(--c-success); }

/* ── Form labels ─────────────────────────────────────────────────────── */
.form-label { font-weight:600; font-size:.82rem; color:var(--c-muted); text-transform:uppercase; letter-spacing:.05em; margin-bottom:.6rem; }
.form-control, .form-select {
    border: 1.5px solid var(--c-border);
    border-radius: var(--radius-sm);
    font-size:.9rem;
    padding:.65rem 1rem;
    transition: border-color .15s, box-shadow .15s;
}
.form-control:focus, .form-select:focus {
    border-color: var(--c-accent);
    box-shadow: 0 0 0 3px rgba(79,70,229,.12);
    outline: none;
}

/* ── Country selector ────────────────────────────────────────────────── */
.country-flag-option { display:flex; align-items:center; gap:.5rem; }

/* ── Network grid ────────────────────────────────────────────────────── */
.network-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(135px,1fr)); gap:.65rem; }
.network-card {
    border: 1.5px solid var(--c-border);
    border-radius: var(--radius-sm);
    padding: .7rem .8rem;
    cursor: pointer;
    transition: all .15s;
    display: flex;
    flex-direction: column;
    gap: .3rem;
    background: var(--c-surface);
    user-select: none;
}
.network-card:hover { border-color:#94a3b8; background:#f8fafc; transform:translateY(-1px); }
.network-card.selected {
    border-color: var(--c-accent);
    background: var(--c-accent-light);
    box-shadow: 0 0 0 1px var(--c-accent);
}
.network-card .net-name { font-weight:700; font-size:.8rem; }
.network-card .net-sub  { font-size:.68rem; color:var(--c-muted); font-family:'DM Mono',monospace; }
.network-card .net-check {
    width:16px;height:16px;border-radius:50%;border:1.5px solid var(--c-border);
    display:flex;align-items:center;justify-content:center;align-self:flex-end;
    margin-top:-.2rem; flex-shrink:0;
}
.network-card.selected .net-check { background:var(--c-accent);border-color:var(--c-accent); }
.network-card.selected .net-check::after { content:'✓'; color:#fff; font-size:.6rem; font-weight:700; }

/* ── Provider tags ───────────────────────────────────────────────────── */
.provider-tag {
    display:inline-block;
    padding:.1rem .4rem;
    border-radius:4px;
    font-size:.62rem;
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:.04em;
}
.tag-touchpay    { background:#d1fae5; color:#065f46; }
.tag-paiementpro { background:#fef3c7; color:#92400e; }
.tag-pawapay     { background:#ede9fe; color:#4c1d95; }
.tag-paystack    { background:#dbeafe; color:#1e40af; }

/* ── OTP box ─────────────────────────────────────────────────────────── */
.otp-box {
    background: #fffbeb;
    border: 1.5px solid #fde68a;
    border-radius: var(--radius-sm);
    padding: 1rem;
    font-size:.85rem;
}
.otp-code { font-family:'DM Mono',monospace; font-size:1.1rem; font-weight:600; color:#92400e; letter-spacing:.1em; }

/* ── Submit button ───────────────────────────────────────────────────── */
.btn-pay {
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    border: none;
    border-radius: var(--radius-sm);
    color: #fff;
    font-weight:700;
    font-size:1rem;
    padding: .9rem 2rem;
    width: 100%;
    transition: all .2s;
    position: relative;
    overflow: hidden;
}
.btn-pay::before {
    content:'';
    position:absolute;inset:0;
    background:rgba(255,255,255,.1);
    opacity:0;transition:.2s;
}
.btn-pay:hover::before { opacity:1; }
.btn-pay:hover { transform:translateY(-1px); box-shadow:0 8px 20px rgba(79,70,229,.35); }
.btn-pay:disabled { opacity:.7; transform:none; cursor:not-allowed; }
.btn-pay .spinner { width:18px;height:18px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:spin .6s linear infinite;display:none; }
@keyframes spin { to { transform:rotate(360deg); } }
.btn-pay.loading .spinner { display:inline-block; }
.btn-pay.loading .btn-text { display:none; }

/* ── Loader networks ─────────────────────────────────────────────────── */
.networks-loading { display:flex;align-items:center;gap:.5rem;color:var(--c-muted);font-size:.85rem;padding:1rem 0; }
.networks-loading .dot { width:6px;height:6px;border-radius:50%;background:var(--c-accent);animation:bounce .8s infinite; }
.networks-loading .dot:nth-child(2){ animation-delay:.15s; }
.networks-loading .dot:nth-child(3){ animation-delay:.3s; }
@keyframes bounce { 0%,100%{transform:translateY(0)}50%{transform:translateY(-4px)} }

/* ── Group header ────────────────────────────────────────────────────── */
.group-header { font-size:.72rem; font-weight:700; color:var(--c-muted); text-transform:uppercase; letter-spacing:.08em; margin:.8rem 0 .4rem; }
</style>
@endpush

@section('content')
<div class="checkout-wrap">

    {{-- ── Panel gauche – Résumé ─────────────────────────────────────────── --}}
    <div class="panel-left">
        @if(isset($book))
            <img src="{{ $book->cover_image ? asset('storage/'.$book->cover_image) : asset('images/default-book-cover.jpg') }}"
                 alt="{{ $book->title }}" class="cover-img">
            <div class="summary-title">{{ $book->title }}</div>
            <div class="summary-sub">{{ $book->author->name ?? __('Auteur') }}</div>
        @elseif(isset($plan))
            <div class="plan-icon">📚</div>
            <div class="summary-title">{{ $plan->name }}</div>
            <div class="summary-sub">{{ $plan->description }}</div>
        @endif

        <hr class="summary-divider">

        @if(isset($book))
        <div class="summary-row">
            <span class="label">{{ __("Type d'achat") }}</span>
            <span class="val">{{ strtoupper($type) }}</span>
        </div>
        @elseif(isset($plan))
        <div class="summary-row">
            <span class="label">{{ __("Durée") }}</span>
            <span class="val">{{ $plan->duration_days }} {{ __('jours') }}</span>
        </div>
        <div class="summary-row">
            <span class="label">{{ __("Type") }}</span>
            <span class="val">{{ $type === 'renewal' ? __('Renouvellement') : __('Nouvel abonnement') }}</span>
        </div>
        @endif

        <hr class="summary-divider">

        <div class="summary-total">
            <div class="label mb-1">{{ __('Total à payer') }}</div>
            <div class="val">{{ number_format($price, 0, ',', ' ') }} <span style="font-size:1rem;font-weight:500;color:rgba(255,255,255,.5)">XOF</span></div>
        </div>

        <div class="trust-badges">
            <div class="badge-item"><i class="fas fa-lock"></i> Paiement sécurisé</div>
            <div class="badge-item"><i class="fas fa-shield-alt"></i> Données chiffrées</div>
            <div class="badge-item"><i class="fas fa-bolt"></i> Accès immédiat</div>
        </div>
    </div>

    {{-- ── Panel droit – Formulaire ──────────────────────────────────────── --}}
    <div class="panel-right">

        {{-- Steps --}}
        <div class="steps mb-4">
            <div class="step done">✓</div>
            <div class="step-line done"></div>
            <div class="step active">2</div>
            <div class="step-line"></div>
            <div class="step pending">3</div>
        </div>

        <h2 class="fw-bold mb-1" style="font-size:1.5rem;">{{ __('Choisir votre moyen de paiement') }}</h2>
        <p class="text-muted mb-4" style="font-size:.9rem;">{{ __('Sélectionnez votre opérateur ou moyen de paiement préféré.') }}</p>

        @if(isset($book))
            <form action="{{ route($type === 'pdf' ? 'purchase.pdf' : 'purchase.audio', $book) }}" method="POST" id="payment-form">
        @elseif(isset($plan))
            <form action="{{ $type === 'renewal' ? route('subscription.renew') : route('subscription.subscribe', $plan) }}" method="POST" id="payment-form">
        @endif
            @csrf
            <input type="hidden" name="network" id="selected-network">

            {{-- Téléphone --}}
            <div class="mb-4">
                <label class="form-label">{{ __('Numéro de téléphone') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-white" style="border:1.5px solid var(--c-border);border-right:0;border-radius:var(--radius-sm) 0 0 var(--radius-sm);">
                        <i class="fas fa-mobile-alt text-muted"></i>
                    </span>
                    <input type="tel" name="phone" id="phone" class="form-control"
                           style="border-left:0;border-radius:0 var(--radius-sm) var(--radius-sm) 0;"
                           placeholder="{{ __('Ex: 0707070707') }}"
                           value="{{ auth()->user()->phone ?? '' }}" required>
                </div>
                <div class="form-text" style="font-size:.75rem;">{{ __('Utilisé pour Mobile Money et les notifications.') }}</div>
            </div>

            {{-- Email (si non connecté) --}}
            @guest
            <div class="mb-4">
                <label class="form-label">{{ __('Adresse email') }}</label>
                <input type="email" name="email" class="form-control" placeholder="{{ __('votre@email.com') }}">
            </div>
            @endguest

            {{-- Pays --}}
            <div class="mb-4">
                <label class="form-label">{{ __('Pays de paiement') }}</label>
                <select class="form-select" id="country-select">
                    @php
                        $countriesList = [
                            'CI' => "🇨🇮 Côte d'Ivoire",
                            'SN' => '🇸🇳 Sénégal',
                            'BJ' => '🇧🇯 Bénin',
                            'BF' => '🇧🇫 Burkina Faso',
                            'ML' => '🇲🇱 Mali',
                            'NE' => '🇳🇪 Niger',
                            'TG' => '🇹🇬 Togo',
                            'CM' => '🇨🇲 Cameroun',
                            'GW' => '🇬🇼 Guinée-Bissau',
                            'CD' => '🇨🇩 RD Congo',
                            'CG' => '🇨🇬 Congo Brazzaville',
                            'GA' => '🇬🇦 Gabon',
                            'GH' => '🇬🇭 Ghana',
                            'KE' => '🇰🇪 Kenya',
                            'MW' => '🇲🇼 Malawi',
                            'RW' => '🇷🇼 Rwanda',
                            'SL' => '🇸🇱 Sierra Leone',
                            'TZ' => '🇹🇿 Tanzanie',
                            'UG' => '🇺🇬 Ouganda',
                            'ZM' => '🇿🇲 Zambie',
                            'NG' => '🇳🇬 Nigéria',
                            'MZ' => '🇲🇿 Mozambique',
                            'MA' => '🇲🇦 Maroc',
                            'FR' => '🇫🇷 France / Europe',
                        ];
                        $defaultCountry = 'CI';
                    @endphp
                    @foreach($countriesList as $iso => $label)
                        <option value="{{ $iso }}" {{ $iso === $defaultCountry ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Méthodes de paiement --}}
            <div class="mb-4">
                <label class="form-label">{{ __('Opérateur / Mode de paiement') }}</label>
                <div id="networks-container">
                    <div class="networks-loading" id="networks-loading">
                        <div class="dot"></div><div class="dot"></div><div class="dot"></div>
                        <span>{{ __('Chargement...') }}</span>
                    </div>
                    <div id="networks-grid" class="d-none"></div>
                    <div id="networks-empty" class="d-none text-center py-3 text-muted" style="font-size:.85rem;">
                        {{ __('Aucun mode de paiement disponible pour ce pays.') }}
                    </div>
                </div>
                <p id="network-error" class="text-danger small mt-1 d-none">{{ __('Veuillez sélectionner un mode de paiement.') }}</p>
            </div>

            {{-- OTP Orange Money --}}
            <div class="otp-box d-none mb-4" id="otp-container">
                <div class="fw-semibold mb-1">🔐 {{ __('Code OTP requis') }}</div>
                <div class="text-muted small mb-2">{{ __('Composez le code suivant sur votre téléphone puis entrez le code reçu.') }}</div>
                <div class="otp-code mb-2">#144*82#</div>
                <input type="text" name="otp" id="otp" class="form-control" placeholder="{{ __('Entrez votre code OTP') }}" maxlength="10">
            </div>

            {{-- Bouton paiement --}}
            <button type="submit" class="btn-pay" id="submit-btn">
                <span class="btn-text d-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-lock"></i>
                    {{ __('Confirmer le paiement') }}
                    · {{ number_format($price, 0, ',', ' ') }} XOF
                </span>
                <div class="spinner mx-auto"></div>
            </button>

            {{-- Logos prestataires --}}
            <div class="d-flex align-items-center justify-content-center gap-3 mt-4 flex-wrap">
                <img src="https://www.paiementpro.net/images/logo.png" alt="PaiementPro" height="22" style="opacity:.5;filter:grayscale(1);">
                <img src="https://api.gutouch.com/img/touchpay.png" alt="TouchPay" height="20" style="opacity:.5;filter:grayscale(1);">
                <span style="color:var(--c-border);font-size:.8rem;">Paystack · PawaPay</span>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    // ── État ──────────────────────────────────────────────────────────────
    let currentMethods  = @json($methods['methods'] ?? []);
    let selectedNetwork = null;

    const gridEl      = document.getElementById('networks-grid');
    const loadingEl   = document.getElementById('networks-loading');
    const emptyEl     = document.getElementById('networks-empty');
    const networkInput= document.getElementById('selected-network');
    const otpBox      = document.getElementById('otp-container');
    const submitBtn   = document.getElementById('submit-btn');
    const netError    = document.getElementById('network-error');
    const countrySelect = document.getElementById('country-select');

    // ── Groupement des méthodes ───────────────────────────────────────────
    function groupMethods(methods) {
        const groups = {};
        methods.forEach(m => {
            if (!groups[m.group]) groups[m.group] = [];
            groups[m.group].push(m);
        });
        return groups;
    }

    const providerTagMap = {
        touchpay:    'tag-touchpay',
        paiementpro: 'tag-paiementpro',
        pawapay:     'tag-pawapay',
        paystack:    'tag-paystack',
    };

    // ── Rendu des méthodes ────────────────────────────────────────────────
    function renderNetworks(methods) {
        loadingEl.classList.add('d-none');
        gridEl.innerHTML = '';

        if (!methods || methods.length === 0) {
            emptyEl.classList.remove('d-none');
            gridEl.classList.add('d-none');
            submitBtn.disabled = true;
            return;
        }

        emptyEl.classList.add('d-none');
        gridEl.classList.remove('d-none');
        submitBtn.disabled = false;

        const groups = groupMethods(methods);

        Object.entries(groups).forEach(([group, items]) => {
            const header = document.createElement('div');
            header.className = 'group-header';
            header.textContent = group;
            gridEl.appendChild(header);

            const row = document.createElement('div');
            row.className = 'network-grid mb-2';

            items.forEach(method => {
                const card = document.createElement('div');
                card.className = 'network-card';
                card.dataset.id = method.id;
                const tagClass = providerTagMap[method.provider] || '';
                card.innerHTML = `
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="net-name" style="color:${method.icon_color}">${method.name}</div>
                        <div class="net-check"></div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="provider-tag ${tagClass}">${method.provider}</span>
                    </div>
                    <div class="net-sub">${method.id}</div>
                `;
                card.addEventListener('click', () => selectNetwork(card, method));
                row.appendChild(card);
            });

            gridEl.appendChild(row);
        });

        // Auto-sélection du premier
        const first = gridEl.querySelector('.network-card');
        if (first) {
            first.click();
        }
    }

    // ── Sélection d'une méthode ───────────────────────────────────────────
    function selectNetwork(cardEl, method) {
        document.querySelectorAll('.network-card').forEach(c => c.classList.remove('selected'));
        cardEl.classList.add('selected');
        selectedNetwork = method.id;
        networkInput.value = method.id;
        netError.classList.add('d-none');

        // OTP Orange Money
        if (method.id === 'OMCIV2') {
            otpBox.classList.remove('d-none');
        } else {
            otpBox.classList.add('d-none');
            document.getElementById('otp').value = '';
        }
    }

    // ── Fetch des méthodes par pays ───────────────────────────────────────
    async function fetchNetworks(country) {
        loadingEl.classList.remove('d-none');
        gridEl.classList.add('d-none');
        emptyEl.classList.add('d-none');
        selectedNetwork = null;
        networkInput.value = '';

        try {
            const resp = await fetch(`{{ route('payment.methods') }}?country=${country}`);
            if (!resp.ok) throw new Error('Network error');
            const data = await resp.json();
            currentMethods = data.methods || [];
            renderNetworks(currentMethods);
        } catch (err) {
            console.error('Fetch networks error:', err);
            loadingEl.classList.add('d-none');
            emptyEl.classList.remove('d-none');
            emptyEl.textContent = '{{ __("Erreur lors du chargement. Réessayez.") }}';
        }
    }

    // ── Événements ───────────────────────────────────────────────────────
    countrySelect.addEventListener('change', e => fetchNetworks(e.target.value));

    document.getElementById('payment-form').addEventListener('submit', function (e) {
        if (!networkInput.value) {
            e.preventDefault();
            netError.classList.remove('d-none');
            gridEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
    });

    // Détection auto du pays depuis le numéro
    const phoneInput = document.getElementById('phone');
    const phonePrefixes = @json(collect($methods['methods'] ?? [])->pluck('id')->values());

    // ── Init ─────────────────────────────────────────────────────────────
    renderNetworks(currentMethods);
})();
</script>
@endpush