<!-- resources/views/auth/register.blade.php -->

@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .iti { width: 100%; }
    .password-strength-meter {
        height: 5px;
        background-color: #eee;
        border-radius: 3px;
        margin-top: 5px;
        overflow: hidden;
    }
    .password-strength-meter-bar {
        height: 100%;
        width: 0;
        transition: width 0.3s ease-in-out, background-color 0.3s ease-in-out;
    }
    .strength-weak { background-color: #ff4d4d; width: 33.33%; }
    .strength-medium { background-color: #ffa64d; width: 66.66%; }
    .strength-strong { background-color: #2eb82e; width: 100%; }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('S\'inscrire') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="first_name" class="col-md-4 col-form-label text-md-end">{{ __('Prénom') }}</label>

                            <div class="col-md-6">
                                <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" autofocus>

                                @error('first_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="last_name" class="col-md-4 col-form-label text-md-end">{{ __('Nom') }}</label>

                            <div class="col-md-6">
                                <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name">

                                @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Email Section -->
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Adresse E-mail') }}</label>

                            <div class="col-md-6">
                                <div class="input-group">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-send-email-code">
                                        <span class="spinner-border spinner-border-sm d-none" id="send-email-spinner" role="status" aria-hidden="true"></span>
                                        <span id="send-email-text">{{ __('Vérifier') }}</span>
                                    </button>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 d-none" id="email-verification-box">
                            <div class="col-md-6 offset-md-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="email_verification_code" placeholder="Code reçu par email">
                                    <button class="btn btn-outline-success" type="button" id="btn-verify-email-code">
                                        <span class="spinner-border spinner-border-sm d-none" id="verify-email-spinner" role="status" aria-hidden="true"></span>
                                        <span id="verify-email-text">{{ __('Valider') }}</span>
                                    </button>
                                </div>
                                <input type="hidden" name="is_verified_email" id="is_verified_email" value="">
                                @error('is_verified_email')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone Section -->
                        <div class="row mb-3">
                            <label for="phone" class="col-md-4 col-form-label text-md-end">{{ __('Numéro de téléphone') }}</label>

                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <input type="tel" id="phone_display" class="form-control" value="{{ old('phone') }}" required>
                                        <input type="hidden" name="phone" id="phone_hidden" value="{{ old('phone') }}">
                                        <input type="hidden" name="country_iso" id="country_iso">
                                    </div>
                                    <button class="btn btn-outline-secondary ms-2" type="button" id="btn-send-phone-code">
                                        <span class="spinner-border spinner-border-sm d-none" id="send-phone-spinner" role="status" aria-hidden="true"></span>
                                        <span id="send-phone-text">{{ __('Vérifier') }}</span>
                                    </button>
                                </div>
                                @error('phone')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 d-none" id="phone-verification-box">
                            <div class="col-md-6 offset-md-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="phone_verification_code" placeholder="Code reçu par WhatsApp">
                                    <button class="btn btn-outline-success" type="button" id="btn-verify-phone-code">
                                        <span class="spinner-border spinner-border-sm d-none" id="verify-phone-spinner" role="status" aria-hidden="true"></span>
                                        <span id="verify-phone-text">{{ __('Valider') }}</span>
                                    </button>
                                </div>
                                <input type="hidden" name="is_verified_phone" id="is_verified_phone" value="">
                                @error('is_verified_phone')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="date_of_birth" class="col-md-4 col-form-label text-md-end">{{ __('Date de naissance (Optionnel)') }}</label>

                            <div class="col-md-6">
                                <input id="date_of_birth" type="text" class="form-control @error('date_of_birth') is-invalid @enderror" name="date_of_birth" value="{{ old('date_of_birth') }}">

                                @error('date_of_birth')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Mot de passe') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                <div class="password-strength-meter">
                                    <div class="password-strength-meter-bar" id="password-strength-bar"></div>
                                </div>
                                <small id="password-strength-text" class="form-text text-muted"></small>

                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirmer le mot de passe') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                <small id="password-match-text" class="form-text mt-1 d-block"></small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="g-recaptcha @error('g-recaptcha-response') is-invalid @enderror" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                                @error('g-recaptcha-response')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                                    {{ __('S\'inscrire') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/fr.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Datepicker
    flatpickr("#date_of_birth", {
        locale: "fr",
        dateFormat: "Y-m-d",
        maxDate: "today",
    });

    // 2. Phone Input Configuration
    const phoneInput = document.querySelector("#phone_display");
    const phoneHiddenInput = document.querySelector("#phone_hidden");
    const countryIsoInput = document.querySelector("#country_iso");

    const iti = window.intlTelInput(phoneInput, {
        initialCountry: "auto",
        geoIpLookup: function(callback) {
            fetch("https://ipinfo.io/json")
                .then(resp => resp.json())
                .then(data => callback(data.country))
                .catch(() => callback("ci"));
        },
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        preferredCountries: ['ci', 'sn', 'bj', 'bf', 'ml', 'ne', 'tg', 'cm', 'fr', 'cd', 'ng', 'gh']
    });

    phoneInput.addEventListener("countrychange", function() {
        const countryData = iti.getSelectedCountryData();
        countryIsoInput.value = (countryData.iso2 || 'ci').toUpperCase();
        updateHiddenPhone();
    });

    phoneInput.addEventListener("input", updateHiddenPhone);
    phoneInput.addEventListener("blur", updateHiddenPhone);

    function updateHiddenPhone() {
        if (iti.isValidNumber()) {
            phoneHiddenInput.value = iti.getNumber();
        } else {
            phoneHiddenInput.value = phoneInput.value;
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

    // 4. AJAX Verification (Email)
    const btnSendEmailCode = document.getElementById('btn-send-email-code');
    const btnVerifyEmailCode = document.getElementById('btn-verify-email-code');
    const emailInput = document.getElementById('email');
    const isVerifiedEmailInput = document.getElementById('is_verified_email');

    // AJAX Verification (Phone)
    const btnSendPhoneCode = document.getElementById('btn-send-phone-code');
    const btnVerifyPhoneCode = document.getElementById('btn-verify-phone-code');
    const isVerifiedPhoneInput = document.getElementById('is_verified_phone');

    const submitBtn = document.getElementById('submit-btn');

    function checkSubmitStatus() {
        if (isVerifiedEmailInput.value === "1" && isVerifiedPhoneInput.value === "1") {
            submitBtn.disabled = false;
        }
    }

    // --- EMAIL ---
    btnSendEmailCode.addEventListener('click', function() {
        const email = emailInput.value;
        if (!email) {
            toastr.error('Veuillez renseigner votre email.');
            return;
        }

        document.getElementById('send-email-spinner').classList.remove('d-none');
        btnSendEmailCode.disabled = true;

        fetch('{{ route('register.send_verification') }}', {
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

        fetch('{{ route('register.verify_code') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ type: 'email', code: code })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('verify-email-spinner').classList.add('d-none');

            if (data.success) {
                toastr.success(data.message);
                isVerifiedEmailInput.value = "1";
                document.getElementById('email-verification-box').classList.add('d-none');
                checkSubmitStatus();
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
    btnSendPhoneCode.addEventListener('click', function() {
        const phone = phoneHiddenInput.value;
        if (!phone || !iti.isValidNumber()) {
            toastr.error('Veuillez renseigner un numéro de téléphone valide.');
            return;
        }

        document.getElementById('send-phone-spinner').classList.remove('d-none');
        btnSendPhoneCode.disabled = true;

        fetch('{{ route('register.send_verification') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ type: 'phone', phone: phone })
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

        fetch('{{ route('register.verify_code') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ type: 'phone', code: code })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('verify-phone-spinner').classList.add('d-none');

            if (data.success) {
                toastr.success(data.message);
                isVerifiedPhoneInput.value = "1";
                document.getElementById('phone-verification-box').classList.add('d-none');
                checkSubmitStatus();
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
