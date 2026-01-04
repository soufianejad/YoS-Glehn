@extends('layouts.app')

@section('title', __('Nous Contacter') . ' - ' . config('platform.name'))

@push('styles')
<style>
    .contact-header {
        padding: 5rem 0;
        background-color: #f8f9fa;
    }
    .contact-info i {
        font-size: 1.5rem;
        width: 40px;
    }
</style>
@endpush

@section('content')
<div class="contact-header text-center">
    <div class="container">
        <h1 class="display-4 font-weight-bold">{{ __('Contactez-Nous') }}</h1>
        <p class="lead text-muted">{{ __('Une question, une suggestion ? Nous sommes à votre écoute.') }}</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        <!-- Contact Form -->
        <div class="col-lg-7">
            <div class="card border-0">
                <div class="card-body p-4 p-md-5">
                    <h3 class="mb-4">{{ __('Envoyer un Message') }}</h3>
                     @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form action="{{ route('contact.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Votre Nom') }}</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required value="{{ old('name') }}">
                             @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Votre Email') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" required value="{{ old('email') }}">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">{{ __('Votre Message') }}</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                            @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">{{ __('Envoyer') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contact Details -->
        <div class="col-lg-5">
            <div class="p-4 p-md-5">
                <h3 class="mb-4">{{ __('Nos Coordonnées') }}</h3>
                <ul class="list-unstyled contact-info">
                    <li class="d-flex align-items-start mb-4">
                        <i class="fas fa-map-marker-alt text-primary mt-1"></i>
                        <div>
                            <h6 class="font-weight-bold">Adresse</h6>
                            <p class="text-muted">123 Rue de l'Exemple, 75000 Paris, France</p>
                        </div>
                    </li>
                    <li class="d-flex align-items-start mb-4">
                        <i class="fas fa-envelope text-primary mt-1"></i>
                         <div>
                            <h6 class="font-weight-bold">Email</h6>
                            <p class="text-muted"><a href="mailto:contact@yosglehn.com">contact@yosglehn.com</a></p>
                        </div>
                    </li>
                    <li class="d-flex align-items-start mb-4">
                        <i class="fas fa-phone-alt text-primary mt-1"></i>
                         <div>
                            <h6 class="font-weight-bold">Téléphone</h6>
                            <p class="text-muted">+33 1 23 45 67 89</p>
                        </div>
                    </li>
                </ul>
                 <div class="mt-4 rounded" style="overflow:hidden;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.991625692359!2d2.292292615674268!3d48.85837007928746!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e29641f4555%3A0x40b82c3688c9460!2sTour%20Eiffel!5e0!3m2!1sfr!2sfr!4v1622552222885!5m2!1sfr!2sfr" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
