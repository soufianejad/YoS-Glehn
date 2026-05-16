@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <style>
        .iti {
            width: 100%;
        }
        .input-group > .iti {
            flex: 1 1 auto;
            width: 1%;
        }
        /* Password Strength Meter */
        .password-strength-meter {
            height: 5px;
            background-color: #e9ecef;
            border-radius: 3px;
            margin-top: 5px;
            overflow: hidden;
        }
        .password-strength-meter-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s ease-in-out, background-color 0.3s ease-in-out;
        }
        .strength-weak { background-color: #dc3545; width: 33%; }
        .strength-medium { background-color: #ffc107; width: 66%; }
        .strength-strong { background-color: #198754; width: 100%; }
    </style>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 text-center">
                    <h4 class="mb-0 text-primary fw-bold">{{ __('Réinitialiser le mot de passe') }}</h4>
                    <p class="text-muted mt-2">{{ __('Vérifiez votre compte avec votre adresse email ou numéro de téléphone pour réinitialiser votre mot de passe.') }}</p>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('password.update') }}" id="reset-password-form">
                        @csrf

                        <!-- Tabs for Email / Phone Verification -->
                        <ul class="nav nav-pills nav-justified mb-4" id="verificationTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-pill fw-medium" id="email-tab" data-bs-toggle="pill" data-bs-target="#email-panel" type="button" role="tab" aria-controls="email-panel" aria-selected="true">
                                    <i class="fas fa-envelope me-2"></i>{{ __('Email') }}
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill fw-medium" id="phone-tab" data-bs-toggle="pill" data-bs-target="#phone-panel" type="button" role="tab" aria-controls="phone-panel" aria-selected="false">
                                    <i class="fab fa-whatsapp me-2"></i>{{ __('Téléphone (WhatsApp)') }}
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="verificationTabsContent">
                            <!-- Email Panel -->
                            <div class="tab-pane fade show active" id="email-panel" role="tabpanel" aria-labelledby="email-tab">
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-bold">{{ __('Adresse E-mail') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-envelope text-muted"></i></span>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Ex: jean.dupont@example.com">
                                        <button class="btn btn-outline-primary" type="button" id="btn-send-email-code">
                                            <span class="spinner-border spinner-border-sm d-none" id="send-email-spinner" role="status" aria-hidden="true"></span>
                                            <span id="send-email-text">{{ __('Envoyer Code') }}</span>
                                        </button>
                                    </div>
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email Verification Code Box -->
                                <div class="mb-3 p-3 border rounded bg-light d-none" id="email-verification-box">
                                    <label for="email_verification_code" class="form-label fw-bold text-success">{{ __('Code reçu par email') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control border-success" id="email_verification_code" placeholder="Ex: 123456">
                                        <button class="btn btn-success" type="button" id="btn-verify-email-code">
                                            <span class="spinner-border spinner-border-sm d-none" id="verify-email-spinner" role="status" aria-hidden="true"></span>
                                            <span id="verify-email-text">{{ __('Vérifier') }}</span>
                                        </button>
                                    </div>
                                    <small class="text-muted mt-1 d-block">{{ __('Veuillez vérifier vos spams si vous ne trouvez pas l\'email.') }}</small>
                                </div>
                            </div>

                            <!-- Phone Panel -->
                            <div class="tab-pane fade" id="phone-panel" role="tabpanel" aria-labelledby="phone-tab">
                                <div class="mb-3">
                                    <label for="phone" class="form-label fw-bold">{{ __('Numéro WhatsApp') }} <span class="text-danger">*</span></label>
                                    <div class="input-group mb-2">
                                        <input type="tel" id="phone" class="form-control" placeholder="Numéro de téléphone">
                                        <input type="hidden" name="phone" id="hidden_phone" value="{{ old('phone') }}">
                                        <input type="hidden" name="country_iso" id="country_iso" value="ci">
                                        <input type="hidden" name="country_code" id="country_code" value="225">
                                    </div>
                                    <button class="btn btn-outline-success w-100 mt-2" type="button" id="btn-send-phone-code">
                                        <span class="spinner-border spinner-border-sm d-none" id="send-phone-spinner" role="status" aria-hidden="true"></span>
                                        <i class="fab fa-whatsapp me-2"></i><span id="send-phone-text">{{ __('Recevoir le code par WhatsApp') }}</span>
                                    </button>
                                    @error('phone')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Phone Verification Code Box -->
                                <div class="mb-3 p-3 border rounded bg-light d-none" id="phone-verification-box">
                                    <label for="phone_verification_code" class="form-label fw-bold text-success">{{ __('Code reçu par WhatsApp') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control border-success" id="phone_verification_code" placeholder="Ex: 123456">
                                        <button class="btn btn-success" type="button" id="btn-verify-phone-code">
                                            <span class="spinner-border spinner-border-sm d-none" id="verify-phone-spinner" role="status" aria-hidden="true"></span>
                                            <span id="verify-phone-text">{{ __('Vérifier') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Inputs for Verification Status -->
                        <input type="hidden" name="type" id="verification_type" value="email">
                        <input type="hidden" id="is_verified" value="0">
                        @error('verification')
                            <div class="text-danger small mb-3 text-center fw-bold">{{ $message }}</div>
                        @enderror

                        <hr class="my-4 text-muted">

                        <!-- New Password Section (Hidden initially) -->
                        <div id="new-password-section" class="d-none">
                            <h5 class="fw-bold mb-3 text-center text-secondary">{{ __('Nouveau mot de passe') }}</h5>
                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">{{ __('Mot de passe') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                </div>
                                <div class="password-strength-meter mt-2">
                                    <div id="password-strength-bar" class="password-strength-meter-bar"></div>
                                </div>
                                <small id="password-strength-text" class="form-text text-muted mt-1 d-block"></small>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password-confirm" class="form-label fw-bold">{{ __('Confirmer le mot de passe') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                </div>
                                <small id="password-match-text" class="form-text mt-1 d-block"></small>
                            </div>

                            <div class="mb-4 d-flex justify-content-center">
                                <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                            </div>
                            @error('g-recaptcha-response')
                                <div class="text-danger small mb-3 text-center">{{ $message }}</div>
                            @enderror

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm fw-bold" id="submit-btn" disabled>
                                    <i class="fas fa-save me-2"></i> {{ __('Réinitialiser le mot de passe') }}
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="card-footer bg-light text-center py-3">
                    <p class="mb-0 text-muted">{{ __('Vous vous souvenez de votre mot de passe ?') }} <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">{{ __('Se connecter') }}</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Tab Management
    const emailTab = document.getElementById('email-tab');
    const phoneTab = document.getElementById('phone-tab');
    const verificationTypeInput = document.getElementById('verification_type');
    const isVerifiedInput = document.getElementById('is_verified');
    const newPasswordSection = document.getElementById('new-password-section');
    const submitBtn = document.getElementById('submit-btn');

    emailTab.addEventListener('shown.bs.tab', function () {
        verificationTypeInput.value = 'email';
        checkVerificationStatus();
    });

    phoneTab.addEventListener('shown.bs.tab', function () {
        verificationTypeInput.value = 'phone';
        checkVerificationStatus();
    });

    // 2. Intl-Tel-Input Initialization
    const phoneInput = document.querySelector("#phone");
    const phoneHiddenInput = document.querySelector("#hidden_phone");
    const countryIsoInput = document.querySelector("#country_iso");
    const countryCodeInput = document.querySelector("#country_code");

    const iti = window.intlTelInput(phoneInput, {
        allowDropdown: false,
        separateDialCode: true,
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        preferredCountries: ['ci', 'sn', 'bj', 'bf', 'ml', 'ne', 'tg', 'cm', 'fr', 'cd', 'ng', 'gh']
    });

    phoneInput.addEventListener("countrychange", function() {
        const countryData = iti.getSelectedCountryData();
        countryIsoInput.value = (countryData.iso2 || 'ci').toUpperCase();
        countryCodeInput.value = countryData.dialCode || '225';
        updateHiddenPhone();
    });

    phoneInput.addEventListener("input", updateHiddenPhone);
    phoneInput.addEventListener("blur", updateHiddenPhone);

    function updateHiddenPhone() {
        const countryData = iti.getSelectedCountryData();
        countryCodeInput.value = countryData.dialCode || '225';

        if (iti.isValidNumber()) {
            phoneHiddenInput.value = iti.getNumber().replace('+', '');
        } else {
            phoneHiddenInput.value = phoneInput.value.replace('+', '');
        }
    }

    // 3. Password Strength & Match
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password-confirm');
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');
    const matchText = document.getElementById('password-match-text');

    passwordInput.addEventListener('input', function() {
        const val = passwordInput.value;
        let strength = 0;

        if (val.length >= 8) strength += 1;
        if (val.match(/[a-z]+/)) strength += 1;
        if (val.match(/[A-Z]+/)) strength += 1;
        if (val.match(/[0-9]+/)) strength += 1;
        if (val.match(/[$@#&!]+/)) strength += 1;

        strengthBar.className = 'password-strength-meter-bar';
        if (val.length === 0) {
            strengthBar.style.width = '0';
            strengthText.innerText = '';
        } else if (strength < 3) {
            strengthBar.classList.add('strength-weak');
            strengthText.innerText = 'Faible';
            strengthText.className = 'form-text text-danger';
        } else if (strength >= 3 && strength < 5) {
            strengthBar.classList.add('strength-medium');
            strengthText.innerText = 'Moyen';
            strengthText.className = 'form-text text-warning';
        } else {
            strengthBar.classList.add('strength-strong');
            strengthText.innerText = 'Fort';
            strengthText.className = 'form-text text-success';
        }
        checkMatch();
    });

    confirmPasswordInput.addEventListener('input', checkMatch);

    function checkMatch() {
        if (confirmPasswordInput.value === '') {
            matchText.innerText = '';
        } else if (confirmPasswordInput.value === passwordInput.value) {
            matchText.innerText = 'Les mots de passe correspondent.';
            matchText.className = 'form-text text-success mt-1 d-block';
        } else {
            matchText.innerText = 'Les mots de passe ne correspondent pas.';
            matchText.className = 'form-text text-danger mt-1 d-block';
        }
    }

    // 4. AJAX Verification Status Check
    function checkVerificationStatus() {
        if (isVerifiedInput.value === "1") {
            newPasswordSection.classList.remove('d-none');
            submitBtn.disabled = false;
            // Hide verification panels if already verified
            document.getElementById('email-panel').classList.add('d-none');
            document.getElementById('phone-panel').classList.add('d-none');
            document.getElementById('verificationTabs').classList.add('d-none');
        } else {
            newPasswordSection.classList.add('d-none');
            submitBtn.disabled = true;
        }
    }

    // Check for URL parameters (Magic Link)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('verified') === '1') {
        isVerifiedInput.value = "1";
        const type = urlParams.get('type') || 'email';
        verificationTypeInput.value = type;

        if (type === 'email') {
            document.getElementById('email').value = urlParams.get('email');
        } else {
            // Phone handling if needed
        }

        checkVerificationStatus();
        toastr.success('{{ __("Vérification réussie. Veuillez définir votre nouveau mot de passe.") }}');
    }

    // --- EMAIL ---
    const btnSendEmailCode = document.getElementById('btn-send-email-code');
    const btnVerifyEmailCode = document.getElementById('btn-verify-email-code');
    const emailInput = document.getElementById('email');

    btnSendEmailCode.addEventListener('click', function() {
        const email = emailInput.value;
        if (!email) {
            toastr.error('Veuillez renseigner votre email.');
            return;
        }

        document.getElementById('send-email-spinner').classList.remove('d-none');
        btnSendEmailCode.disabled = true;

        fetch('{{ route('password.send-code') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ type: 'email', email: email })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('send-email-spinner').classList.add('d-none');
            btnSendEmailCode.disabled = false;

            if (data.success) {
                toastr.success(data.message);
                document.getElementById('email-verification-box').classList.remove('d-none');
                btnSendEmailCode.classList.add('d-none');
                emailInput.readOnly = true;
            } else {
                toastr.error(data.message || 'Une erreur est survenue.');
            }
        })
        .catch(err => {
            document.getElementById('send-email-spinner').classList.add('d-none');
            btnSendEmailCode.disabled = false;
            toastr.error('Erreur de connexion.');
        });
    });

    btnVerifyEmailCode.addEventListener('click', function() {
        const code = document.getElementById('email_verification_code').value;
        if (!code) {
            toastr.error('Veuillez entrer le code reçu par email.');
            return;
        }

        document.getElementById('verify-email-spinner').classList.remove('d-none');
        btnVerifyEmailCode.disabled = true;

        fetch('{{ route('password.verify-code') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ type: 'email', code: code })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('verify-email-spinner').classList.add('d-none');

            if (data.success) {
                toastr.success(data.message);
                isVerifiedInput.value = "1";
                document.getElementById('email-verification-box').classList.add('d-none');
                checkVerificationStatus();
            } else {
                btnVerifyEmailCode.disabled = false;
                toastr.error(data.message || 'Code incorrect.');
            }
        })
        .catch(err => {
            document.getElementById('verify-email-spinner').classList.add('d-none');
            btnVerifyEmailCode.disabled = false;
            toastr.error('Erreur de connexion.');
        });
    });

    // --- PHONE ---
    const btnSendPhoneCode = document.getElementById('btn-send-phone-code');
    const btnVerifyPhoneCode = document.getElementById('btn-verify-phone-code');

    btnSendPhoneCode.addEventListener('click', function() {
        const phone = phoneHiddenInput.value;
        if (!phone || !iti.isValidNumber()) {
            toastr.error('Veuillez renseigner un numéro de téléphone valide.');
            return;
        }

        document.getElementById('send-phone-spinner').classList.remove('d-none');
        btnSendPhoneCode.disabled = true;

        fetch('{{ route('password.send-code') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ type: 'phone', phone: phone, country_code: countryCodeInput.value })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('send-phone-spinner').classList.add('d-none');
            btnSendPhoneCode.disabled = false;

            if (data.success) {
                toastr.success(data.message);
                document.getElementById('phone-verification-box').classList.remove('d-none');
                btnSendPhoneCode.classList.add('d-none');
                phoneInput.readOnly = true;
            } else {
                toastr.error(data.message || 'Une erreur est survenue.');
            }
        })
        .catch(err => {
            document.getElementById('send-phone-spinner').classList.add('d-none');
            btnSendPhoneCode.disabled = false;
            toastr.error('Erreur de connexion.');
        });
    });

    btnVerifyPhoneCode.addEventListener('click', function() {
        const code = document.getElementById('phone_verification_code').value;
        if (!code) {
            toastr.error('Veuillez entrer le code reçu par WhatsApp.');
            return;
        }

        document.getElementById('verify-phone-spinner').classList.remove('d-none');
        btnVerifyPhoneCode.disabled = true;

        fetch('{{ route('password.verify-code') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ type: 'phone', code: code })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('verify-phone-spinner').classList.add('d-none');

            if (data.success) {
                toastr.success(data.message);
                isVerifiedInput.value = "1";
                document.getElementById('phone-verification-box').classList.add('d-none');
                checkVerificationStatus();
            } else {
                btnVerifyPhoneCode.disabled = false;
                toastr.error(data.message || 'Code incorrect.');
            }
        })
        .catch(err => {
            document.getElementById('verify-phone-spinner').classList.add('d-none');
            btnVerifyPhoneCode.disabled = false;
            toastr.error('Erreur de connexion.');
        });
    });
});
</script>
@endpush
