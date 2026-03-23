<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('platform.name'))</title>
    <meta name="description" content="@yield('meta_description', config('platform.seo.default_meta_description'))">
    <meta name="keywords" content="@yield('meta_keywords', config('platform.seo.default_meta_keywords'))">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', config('platform.name'))">
    <meta property="og:description" content="@yield('meta_description', config('platform.seo.default_meta_description'))">
    <meta property="og:image" content="{{ asset(config('platform.seo.og_image')) }}">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset(config('platform.favicon')) }}">
    
    <!-- PWA Manifest -->
    @if(config('platform.pwa.enabled'))
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <meta name="theme-color" content="{{ config('platform.pwa.theme_color') }}">
    <link rel="apple-touch-icon" href="{{ asset('/icon-192x192.png') }}">
    @endif
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="bg-light">
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset(config('platform.logo')) }}" alt="{{ config('platform.name') }}" height="40">
                <span class="ms-2 fw-bold">{{ config('platform.name') }}</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">
                            <i class="bi bi-house-door"></i> {{ __('Accueil') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('library.index') }}">
                            <i class="bi bi-book"></i> {{ __('Bibliothèque') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('library.popular') }}">
                            <i class="bi bi-fire"></i> {{ __('Populaires') }}
                        </a>
                    </li>
                    
                    @auth
                        @if(auth()->user()->isStudent())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('student.dashboard') }}">
                                    <i class="bi bi-mortarboard"></i> {{ __('Mon Espace') }}
                                </a>
                            </li>
                        @elseif(auth()->user()->isAuthor())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('author.dashboard') }}">
                                    <i class="bi bi-pen"></i> {{ __('Mes Livres') }}
                                </a>
                            </li>
                        @elseif(auth()->user()->isSchool())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('school.dashboard') }}">
                                    <i class="bi bi-building"></i> {{ __('Mon École') }}
                                </a>
                            </li>
                        @elseif(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-speedometer2"></i> {{ __('Admin') }}
                                </a>
                            </li>
                        @elseif(auth()->user()->isAdultReader())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('adult.dashboard') }}">
                                    <i class="bi bi-person-circle"></i> {{ __('Espace Adulte') }}
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}">
                                    <i class="bi bi-person-circle"></i> {{ __('Mon Compte') }}
                                </a>
                            </li>
                        @endif
                        
                        <!-- Notifications -->
                        <li class="nav-item dropdown">
                            <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-bell"></i>
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ auth()->user()->unreadNotifications->count() }}
                                    </span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                                @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                                    <li>
                                        <a class="dropdown-item" href="{{ $notification->link ?? '#' }}">
                                            <strong>{{ $notification->title }}</strong><br>
                                            <small class="text-muted">{{ $notification->message }}</small>
                                        </a>
                                    </li>
                                @empty
                                    <li><span class="dropdown-item text-muted">{{ __('Aucune notification') }}</span></li>
                                @endforelse
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center" href="#">{{ __('Voir toutes') }}</a></li>
                            </ul>
                        </li>
                        
                        <!-- Profil utilisateur -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ __('Avatar') }}" class="rounded-circle" width="30" height="30">
                                {{ auth()->user()->first_name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person"></i> {{ __('Mon Profil') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('subscription.index') }}"><i class="bi bi-credit-card"></i> {{ __('Abonnement') }}</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right"></i> {{ __('Déconnexion') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right"></i> {{ __('Connexion') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm ms-2" href="{{ route('register') }}">
                                {{ __("S'inscrire") }}
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Messages Flash -->
    @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    
    @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    
    @if(session('warning'))
        <div class="container mt-3">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    
    <!-- Contenu principal -->
    <main >
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">{{ config('platform.name') }}</h5>
                    <p>{{ config('platform.description') }}</p>
                    <div class="d-flex gap-3 mt-3">
                        @if(config('platform.social.facebook'))
                            <a href="{{ config('platform.social.facebook') }}" class="text-white"><i class="bi bi-facebook fs-4"></i></a>
                        @endif
                        @if(config('platform.social.twitter'))
                            <a href="{{ config('platform.social.twitter') }}" class="text-white"><i class="bi bi-twitter fs-4"></i></a>
                        @endif
                        @if(config('platform.social.instagram'))
                            <a href="{{ config('platform.social.instagram') }}" class="text-white"><i class="bi bi-instagram fs-4"></i></a>
                        @endif
                        @if(config('platform.social.youtube'))
                            <a href="{{ config('platform.social.youtube') }}" class="text-white"><i class="bi bi-youtube fs-4"></i></a>
                        @endif
                    </div>
                </div>
                
                <div class="col-md-2 mb-4">
                    <h6 class="mb-3">{{ __('Navigation') }}</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">{{ __('Accueil') }}</a></li>
                        <li><a href="{{ route('library.index') }}" class="text-white-50 text-decoration-none">{{ __('Bibliothèque') }}</a></li>
                        <li><a href="{{ route('subscription.plans') }}" class="text-white-50 text-decoration-none">{{ __('Abonnements') }}</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3 mb-4">
                    <h6 class="mb-3">{{ __('Espaces') }}</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('library.index') }}" class="text-white-50 text-decoration-none">{{ __('Espace Public') }}</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">{{ __('Espace Éducatif') }}</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">{{ __('Devenir Auteur') }}</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">{{ __('Inscription École') }}</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3 mb-4">
                    <h6 class="mb-3">{{ __('Contact') }}</h6>
                    <ul class="list-unstyled text-white-50">
                        <li><i class="bi bi-envelope"></i> {{ config('platform.contact_email') }}</li>
                        <li><i class="bi bi-telephone"></i> {{ config('platform.contact_phone') }}</li>
                        <li><i class="bi bi-geo-alt"></i> {{ config('platform.address') }}</li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4 bg-white">
            
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <small>&copy; {{ date('Y') }} {{ config('platform.name') }}. Tous droits réservés.</small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <small>
                        <a href="#" class="text-white-50 text-decoration-none me-3">{{ __("Conditions d'utilisation") }}</a>
                        <a href="#" class="text-white-50 text-decoration-none me-3">{{ __('Politique de confidentialité') }}</a>
                        <a href="#" class="text-white-50 text-decoration-none">{{ __('Mentions légales') }}</a>
                    </small>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery (si nécessaire) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    
    @if(config('platform.pwa.enabled'))
    <!-- PWA Service Worker -->
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => console.log('Service Worker enregistré'))
                .catch(error => console.log('Erreur Service Worker:', error));
        }
    </script>
    @endif
    
    @stack('scripts')
</body>
</html>