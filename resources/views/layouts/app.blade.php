<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
      /* ── Variables ── */
        :root {
            --nav-height: 64px;
            --nav-bg: #ffffff;
            --nav-border: rgba(0,0,0,.07);
            --nav-shadow: 0 1px 12px rgba(0,0,0,.06);
            --nav-active-color: #0d6efd;
            --nav-text: #343a40;
            --nav-muted: #6c757d;
            --nav-hover-bg: #f8f9fa;
            --nav-separator: rgba(0,0,0,.06);
            --nav-radius: 10px;
            --transition: .18s ease;
        }

        /* ── Navbar base ── */
        .navbar {
            min-height: var(--nav-height);
            background: var(--nav-bg) !important;
            border-bottom: 1px solid var(--nav-border);
            box-shadow: var(--nav-shadow);
            padding: .75rem 0;
            overflow: visible; /* Important for dropdowns to be visible */
        }

        .navbar .container {
            height: 100%;
            flex-wrap: nowrap;
            align-items: center;
        }

        /* ── Brand ── */
        .navbar-brand {
            padding: 0;
            height: 100%;
            display: flex;
            align-items: center;
        }

        .navbar-brand img {
            height: 34px;
        }

        /* ── Toggler ── */
        .navbar-toggler {
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            padding: 6px 8px;
            border-radius: 8px;
            transition: background var(--transition);
            color: var(--nav-text);
        }

        .navbar-toggler:hover {
            background: var(--nav-hover-bg);
        }

        .navbar-toggler-icon {
            width: 20px;
            height: 20px;
        }

        /* ── Desktop nav-link ── */
        .navbar .nav-link {
            color: var(--nav-text) !important;
            font-size: .875rem;
            font-weight: 500;
            padding: .4rem .75rem !important;
            border-radius: 8px;
            transition: background var(--transition), color var(--transition);
            white-space: nowrap;
        }

        .navbar .nav-link:hover {
            background: var(--nav-hover-bg);
            color: var(--nav-active-color) !important;
        }

        .navbar .nav-link.active,
        .navbar .nav-item.active .nav-link {
            color: var(--nav-active-color) !important;
        }

        /* Lien dashboard mis en avant */
        .navbar .nav-link.fw-bold.text-primary {
            background: rgba(13,110,253,.06);
            color: var(--nav-active-color) !important;
        }

        /* ── General Dropdown styling ── */
        .navbar .dropdown-menu {
            position: absolute; /* Crucial for overlaying */
            top: 100%; /* Position right below the toggler */
            margin-top: 6px; /* Space between toggler and menu */
            z-index: 1050;
            border: 1px solid var(--nav-border);
            box-shadow: 0 8px 24px rgba(0,0,0,.10);
            border-radius: var(--nav-radius);
            padding: .5rem;
            min-width: 160px; /* Adjust as needed */
            /* Ensure it defaults to right alignment for most dropdowns, or specify left/right per case */
            right: 0;
            left: auto;
        }

        /* Fix for specific dropdowns in the ms-auto group */
        .navbar-nav.ms-auto .nav-item.dropdown {
            position: relative; /* Make nav-item the positioning context for its dropdown */
        }

        .navbar-nav.ms-auto .nav-item.dropdown .dropdown-menu {
            /* Position relative to the parent nav-item, not the entire navbar */
            left: 0;
            right: auto; /* Align to the left of the parent nav-item */
            min-width: fit-content; /* Adjust width to content if needed */
            /* You might need to adjust top further if there's padding issues */
            top: calc(100% + 6px); /* Ensure consistent spacing */
        }

        /* If you want the currency/language dropdowns to align to the right of their toggler */
        /* You can add dropdown-menu-end to their specific dropdown-menu element */
        .navbar-nav.ms-auto .nav-item.dropdown .dropdown-menu.dropdown-menu-end {
            right: 0;
            left: auto;
        }


        .navbar .dropdown-item {
            border-radius: 6px;
            font-size: .875rem;
            padding: .45rem .75rem;
            color: var(--nav-text);
            transition: background var(--transition);
        }

        .navbar .dropdown-item:hover {
            background: var(--nav-hover-bg);
        }

        .navbar .dropdown-item.active {
            background: rgba(13,110,253,.08);
            color: var(--nav-active-color);
        }

        /* ── Cloche ── */
        .bell-btn {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background var(--transition);
            position: relative;
            padding: 0 !important;
            flex-shrink: 0;
        }

        .bell-btn:hover {
            background: var(--nav-hover-bg);
        }

        .bell-btn .bi-bell {
            font-size: 1.05rem;
            color: var(--nav-text);
        }

        .bell-btn .badge {
            position: absolute !important;
            top: 2px;
            right: 2px;
            min-width: 16px;
            height: 16px;
            padding: 0 4px;
            line-height: 16px;
            font-size: .6rem;
            border-radius: 8px;
        }

        /* Notifications panel */
        .notifications-panel {
            width: 300px;
            max-height: 400px;
            overflow-y: auto;
            border-radius: var(--nav-radius) !important;
        }

        .dropdown-header {
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--nav-muted);
            padding: .5rem .75rem;
        }

        /* ── Avatar ── */
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(13,110,253,.15);
        }

        /* ── MOBILE overlay ── */
        @media (max-width: 767.98px) {
            /* Drawer depuis la droite */
            .navbar-collapse {
                position: fixed !important;
                top: 0 !important;
                right: 0 !important;
                left: auto !important;
                width: 78vw !important;
                max-width: 300px !important;
                height: 100dvh !important;
                background: var(--nav-bg);
                z-index: 1050;
                overflow-y: auto;
                overflow-x: hidden;
                padding: 1.25rem 1.25rem 2.5rem;
                transform: translateX(105%);
                opacity: 1;
                pointer-events: none;
                transition: transform .3s cubic-bezier(.4,0,.2,1);
                border-left: 1px solid var(--nav-separator);
                box-shadow: -8px 0 32px rgba(0,0,0,.10);
                margin: 0 !important;
                display: block !important;
            }

            .navbar-collapse.show {
                transform: translateX(0) !important;
                pointer-events: auto;
            }

            /* Header du drawer */
            .drawer-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding-bottom: 1rem;
                margin-bottom: .25rem;
                border-bottom: 1px solid var(--nav-separator);
            }

            .drawer-close {
                width: 34px;
                height: 34px;
                border: none;
                background: var(--nav-hover-bg);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.1rem;
                color: var(--nav-muted);
                cursor: pointer;
                transition: background var(--transition), color var(--transition);
                flex-shrink: 0;
            }

            .drawer-close:hover {
                background: #e9ecef;
                color: var(--nav-text);
            }
                /* This media query section seems to have a stray `margin-bottom: 1.5rem;` here */
                /* I'm assuming it's part of a previous rule that was cut off. */
                /* If it's causing issues, it might need to be moved or removed. */
            }

            .mobile-section-label {
                font-size: .7rem;
                font-weight: 700;
                letter-spacing: .08em;
                text-transform: uppercase;
                color: var(--nav-muted);
                padding: 0 .25rem .5rem;
                border-bottom: 1px solid var(--nav-separator);
                margin-bottom: .5rem;
            }

            /* Liens mobiles : gros, tactiles */
            .navbar-collapse .nav-link {
                display: flex;
                align-items: center;
                gap: .65rem;
                padding: .75rem .75rem !important;
                border-radius: 10px !important;
                font-size: .95rem !important;
                font-weight: 500;
                margin-bottom: 2px;
            }

            .navbar-collapse .nav-link i {
                font-size: 1.05rem;
                width: 22px;
                text-align: center;
                color: var(--nav-muted);
                flex-shrink: 0;
            }

            .navbar-collapse .nav-link:hover {
                background: var(--nav-hover-bg);
            }

            /* Dropdown mobile : inline */
            .navbar-collapse .dropdown-menu {
                position: static !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                background: transparent;
                padding: 0 0 0 .5rem;
                margin: 0 !important;
            }

            .navbar-collapse .dropdown-item {
                padding: .55rem .75rem;
                border-radius: 8px;
                font-size: .9rem;
                margin-bottom: 1px;
            }

            /* Profil utilisateur en haut du menu mobile */
            .mobile-user-card {
                display: flex;
                align-items: center;
                gap: .85rem;
                padding: .85rem 1rem;
                background: var(--nav-hover-bg);
                border-radius: 12px;
                margin-bottom: 1.25rem;
                text-decoration: none;
                color: var(--nav-text);
            }

            .mobile-user-card img {
                width: 44px;
                height: 44px;
                border-radius: 50%;
                object-fit: cover;
                border: 2px solid rgba(13,110,253,.18);
                flex-shrink: 0;
            }

            .mobile-user-card .user-name {
                font-weight: 600;
                font-size: .95rem;
                line-height: 1.2;
            }

            .mobile-user-card .user-role {
                font-size: .78rem;
                color: var(--nav-muted);
            }

            /* Cloche mobile : masquée (déjà dans la barre) */
            .mobile-bell-wrapper {
                display: flex;
            }

            /* Boutons auth mobiles */
            .mobile-auth-buttons {
                display: flex;
                flex-direction: column;
                gap: .5rem;
                margin-top: .5rem;
            }

            .mobile-auth-buttons a {
                text-align: center;
                padding: .7rem 1rem;
                border-radius: 10px;
                font-weight: 500;
                font-size: .95rem;
            }
        }

        /* ── Overlay backdrop (mobile) ── */
        #nav-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.25);
            z-index: 1039;
            backdrop-filter: blur(2px);
        }

        #nav-backdrop.active {
            display: block;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Backdrop mobile -->
    <div id="nav-backdrop"></div>

    <div id="app" class="d-flex flex-column min-vh-100">
        <nav class="navbar navbar-expand-md navbar-light" style="z-index: 1045">
            <div class="container d-flex align-items-center">

                <!-- Logo -->
                <a class="navbar-brand me-3" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.jpg') }}" alt="{{ __('Logo') }}">
                </a>

                <!-- Actions mobiles (cloche + toggler) -->
                <div class="d-flex d-md-none align-items-center ms-auto gap-2">

                    @auth
                    <!-- Cloche mobile -->
                    <div class="dropdown">
                        <a class="bell-btn nav-link" href="#" role="button"
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            <span class="badge bg-danger d-none unread-notifications-count"
                                  style="position:absolute;top:2px;right:2px;padding:2px 5px;border-radius:50%;font-size:.6rem;min-width:16px;">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 notifications-panel">
                            <h6 class="dropdown-header">{{ __('Notifications') }}</h6>
                            <div class="notifications-list-container"></div>
                        </div>
                    </div>
                    @endauth

                    <!-- Toggler -->
                    <button class="navbar-toggler" type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent"
                            aria-controls="navbarSupportedContent"
                            aria-expanded="false"
                            aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                <!-- Collapse -->
                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                    {{-- ── Header drawer ── --}}
                    {{-- <div class="drawer-header">
                        <a href="{{ url('/') }}" class="d-flex align-items-center text-decoration-none gap-2">
                            <img src="{{ asset('images/logo.jpg') }}" alt="Logo" height="28">
                        </a>
                        <button class="drawer-close" id="drawer-close-btn" aria-label="Fermer">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div> --}}

                    {{-- ── MENU MOBILE uniquement ── --}}
                    <div class="d-md-none">

                        @auth
                        {{-- Carte profil --}}
                        <a class="mobile-user-card" href="#">
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}">
                            <div>
                                <div class="user-name">{{ Auth::user()->name }}</div>
                                <div class="user-role">{{ __('Mon compte') }}</div>
                            </div>
                        </a>
                        @endauth

                        {{-- Section Navigation --}}
                        <div class="mobile-section">
                            <div class="mobile-section-label">{{ __('Navigation') }}</div>
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('library.index') }}">
                                        <i class="bi bi-book"></i>{{ __('Bibliothèque') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('subscription.plans') }}">
                                        <i class="bi bi-star"></i>{{ __('Abonnements') }}
                                    </a>
                                </li>
                                @auth
                                <li class="nav-item">
                                    <a class="nav-link text-primary fw-bold" href="
                                        @if(Auth::user()->isAdmin()) {{ route('admin.dashboard') }}
                                        @elseif(Auth::user()->isAuthor()) {{ route('author.dashboard') }}
                                        @elseif(Auth::user()->isSchool()) {{ route('school.dashboard') }}
                                        @elseif(Auth::user()->isTeacher()) {{ route('teacher.dashboard') }}
                                        @elseif(Auth::user()->isStudent()) {{ route('student.dashboard') }}
                                        @elseif(Auth::user()->isParent()) {{ route('parent.dashboard') }}
                                        @elseif(Auth::user()->isAdultReader()) {{ route('adult.dashboard') }}
                                        @else {{ route('dashboard') }}
                                        @endif
                                    ">
                                        <i class="bi bi-grid"></i>{{ __('Mon Tableau de Bord') }}
                                    </a>
                                </li>
                                @endauth
                            </ul>
                        </div>

                        {{-- Section Préférences --}}
                        <div class="mobile-section">
                            <div class="mobile-section-label">{{ __('Préférences') }}</div>
                            <ul class="navbar-nav">
                                {{-- Langue --}}
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button"
                                       data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-translate"></i>{{ __('Langue') }} — {{ strtoupper(app()->getLocale()) }}
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item @if(app()->getLocale() == 'en') active @endif"
                                           href="{{ route('change.language', 'en') }}">{{ __('English') }}</a>
                                        <a class="dropdown-item @if(app()->getLocale() == 'fr') active @endif"
                                           href="{{ route('change.language', 'fr') }}">{{ __('French') }}</a>
                                    </div>
                                </li>
                                {{-- Devise --}}
                                @if(!empty($availableCurrencies) && count($availableCurrencies) > 0)
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button"
                                       data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-currency-exchange"></i>{{ __('Devise') }} — {{ session('currency', 'XOF') }}
                                    </a>
                                    <div class="dropdown-menu">
                                        @foreach($availableCurrencies as $currency)
                                        <a class="dropdown-item @if(session('currency', 'XOF') == $currency['code']) active @endif"
                                           href="{{ route('change.currency', $currency['code']) }}">{{ $currency['code'] }}</a>
                                        @endforeach
                                    </div>
                                </li>
                                @endif
                            </ul>
                        </div>

                        {{-- Section Compte --}}
                        @auth
                        <div class="mobile-section">
                            <div class="mobile-section-label">{{ __('Compte') }}</div>
                            @include('partials.user-dropdown')
                        </div>
                        @else
                        <div class="mobile-auth-buttons">
                            @if (Route::has('login'))
                            <a class="btn btn-outline-primary" href="{{ route('login') }}">{{ __('Login') }}</a>
                            @endif
                            @if (Route::has('register'))
                            <a class="btn btn-primary" href="{{ route('register') }}">{{ __('Register') }}</a>
                            @endif
                        </div>
                        @endauth

                    </div>{{-- /d-md-none --}}

                    {{-- ── DESKTOP uniquement ── --}}
                    <ul class="navbar-nav me-auto d-none d-md-flex">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('library.index') }}">{{ __('Bibliothèque') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('subscription.plans') }}">{{ __('Abonnements') }}</a>
                        </li>
                        @auth
                        <li class="nav-item">
                            <a class="nav-link fw-bold text-primary" href="
                                @if(Auth::user()->isAdmin()) {{ route('admin.dashboard') }}
                                @elseif(Auth::user()->isAuthor()) {{ route('author.dashboard') }}
                                @elseif(Auth::user()->isSchool()) {{ route('school.dashboard') }}
                                @elseif(Auth::user()->isTeacher()) {{ route('teacher.dashboard') }}
                                @elseif(Auth::user()->isStudent()) {{ route('student.dashboard') }}
                                @elseif(Auth::user()->isParent()) {{ route('parent.dashboard') }}
                                @elseif(Auth::user()->isAdultReader()) {{ route('adult.dashboard') }}
                                @else {{ route('dashboard') }}
                                @endif
                            ">{{ __('Mon Tableau de Bord') }}</a>
                        </li>
                        @endauth
                    </ul>

                    <ul class="navbar-nav ms-auto align-items-md-center d-none d-md-flex">
d
    {{-- Devise desktop --}}
    @if(!empty($availableCurrencies) && count($availableCurrencies) > 0)
    <li class="nav-item dropdown me-1">
        <a class="nav-link dropdown-toggle" href="#" role="button"
           data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-currency-exchange"></i> {{ session('currency', 'XOF') }}
        </a>
        <div class="dropdown-menu dropdown-menu-end"> {{-- ADDED dropdown-menu-end --}}
            @foreach($availableCurrencies as $currency)
            <a class="dropdown-item @if(session('currency', 'XOF') == $currency['code']) active @endif"
               href="{{ route('change.currency', $currency['code']) }}">{{ $currency['code'] }}</a>
            @endforeach
        </div>
    </li>
    @endif

    {{-- Langue desktop --}}
    <li class="nav-item dropdown me-1">
        <a class="nav-link dropdown-toggle" href="#" role="button"
           data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-translate"></i> {{ strtoupper(app()->getLocale()) }}
        </a>
        <div class="dropdown-menu dropdown-menu-end"> {{-- ADDED dropdown-menu-end --}}
            <a class="dropdown-item @if(app()->getLocale() == 'en') active @endif"
               href="{{ route('change.language', 'en') }}">{{ __('English') }}</a>
            <a class="dropdown-item @if(app()->getLocale() == 'fr') active @endif"
               href="{{ route('change.language', 'fr') }}">{{ __('French') }}</a>
        </div>
    </li>

    @guest
        @if (Route::has('login'))
        <li class="nav-item">
            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
        </li>
        @endif
        @if (Route::has('register'))
        <li class="nav-item">
            <a class="btn btn-primary btn-sm ms-2" href="{{ route('register') }}">{{ __('Register') }}</a>
        </li>
        @endif
    @else
        {{-- Cloche desktop --}}
        <li class="nav-item dropdown me-1">
            <a class="bell-btn nav-link" href="#" role="button"
               data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell"></i>
                <span class="badge bg-danger d-none unread-notifications-count"
                      style="position:absolute;top:2px;right:2px;padding:2px 5px;border-radius:50%;font-size:.6rem;min-width:16px;">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 notifications-panel">
                <h6 class="dropdown-header bg-light py-2">{{ __('Notifications') }}</h6>
                <div class="notifications-list-container"></div>
                <div class="dropdown-divider mb-0"></div>
                <a class="dropdown-item text-center small py-2" href="#">{{ __('View all notifications') }}</a>
            </div>
        </li>

        {{-- Avatar --}}
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
               href="#" role="button"
               data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ Auth::user()->avatar_url }}"
                     alt="{{ Auth::user()->name }}"
                     class="user-avatar">
                <span>{{ Auth::user()->name }}</span>
            </a>
            @include('partials.user-dropdown') {{-- This partial should also have dropdown-menu-end if needed --}}
        </li>
    @endguest
</ul>

                </div>{{-- /navbar-collapse --}}
            </div>
        </nav>

        <main class="flex-grow-1">
            @yield('content')
        </main>

        <footer class="bg-light py-4 mt-auto border-top">
            <div class="container text-center">
                <p class="mb-2 text-muted small">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. {{ __('All rights reserved.') }}</p>
                @if(!empty($availableCurrencies) && count($availableCurrencies) > 0)
                <div class="dropup d-inline-block ms-2">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-currency-exchange me-1"></i> {{ session('currency', 'XOF') }}
                    </button>
                    <ul class="dropdown-menu">
                        @foreach($availableCurrencies as $currency)
                        <li><a class="dropdown-item @if(session('currency', 'XOF') == $currency['code']) active @endif"
                               href="{{ route('change.currency', $currency['code']) }}">{{ $currency['code'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <div class="dropup d-inline-block ms-2">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-translate me-1"></i> {{ __('Language') }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item @if(app()->getLocale() == 'en') active @endif" href="{{ route('change.language', 'en') }}">{{ __('English') }}</a></li>
                        <li><a class="dropdown-item @if(app()->getLocale() == 'fr') active @endif" href="{{ route('change.language', 'fr') }}">{{ __('French') }}</a></li>
                    </ul>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        // ── Drawer mobile ──
        const toggler = document.querySelector('.navbar-toggler');
        const backdrop = document.getElementById('nav-backdrop');
        const navCollapse = document.getElementById('navbarSupportedContent');
        const closeBtn = document.getElementById('drawer-close-btn');

        function openDrawer() {
            navCollapse.classList.add('show');
            backdrop.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            navCollapse.classList.remove('show');
            backdrop.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (toggler) {
            toggler.addEventListener('click', function(e) {
                e.stopPropagation();
                if (navCollapse.classList.contains('show')) {
                    closeDrawer();
                } else {
                    openDrawer();
                }
            });
        }

        if (backdrop) backdrop.addEventListener('click', closeDrawer);
        if (closeBtn) closeBtn.addEventListener('click', closeDrawer);

        // Fermer sur clic d'un lien (pas les dropdowns)
        navCollapse && navCollapse.querySelectorAll('.nav-link:not(.dropdown-toggle)').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) closeDrawer();
            });
        });

        // ── CSRF ──
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        // ── Favoris ──
        $(document).on('submit', '.favorite-form', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var button = form.find('button[type="submit"]');
            var icon = button.find('i');
            var buttonTextSpan = button.find('#favorite-button-text');

            $.ajax({
                type: 'POST', url: url, data: form.serialize(),
                success: function(response) {
                    toastr.success(response.message);
                    if (response.status === 'favorited') {
                        icon.removeClass('far fa-heart').addClass('fas fa-heart');
                        button.removeClass('btn-outline-danger').addClass('btn-danger');
                        if (buttonTextSpan.length) {
                            buttonTextSpan.text(@json(__('Retirer des favoris')));
                        } else {
                            button.html('<i class="fas fa-heart me-2"></i> ' + @json(__('Retirer des favoris')));
                        }
                    } else {
                        icon.removeClass('fas fa-heart').addClass('far fa-heart');
                        button.removeClass('btn-danger').addClass('btn-outline-danger');
                        if (buttonTextSpan.length) {
                            buttonTextSpan.text(@json(__('Ajouter aux favoris')));
                        } else {
                            button.html('<i class="far fa-heart me-2"></i> ' + @json(__('Ajouter aux favoris')));
                        }
                        if (form.closest('.book-card-col').length) {
                            form.closest('.book-card-col').fadeOut();
                        }
                    }
                },
                error: function() { toastr.error(@json(__('An error occurred. Please try again.'))); }
            });
        });

        // ── Notifications ──
        @auth
        function fetchNotifications() {
            $.ajax({
                url: @json(route('api.notifications.index')),
                method: 'GET',
                success: function(response) {
                    let countEls = $('.unread-notifications-count');
                    let markAllBtn = $('#mark-all-read-btn');
                    countEls.text(response.unread_count);
                    if (response.unread_count > 0) {
                        countEls.removeClass('d-none');
                        markAllBtn.removeClass('d-none');
                    } else {
                        countEls.addClass('d-none');
                        markAllBtn.addClass('d-none');
                    }
                    let containers = $('.notifications-list-container');
                    containers.empty();
                    if (response.notifications.length > 0) {
                        response.notifications.forEach(function(n) {
                            containers.append(`
                                <div class="dropdown-item border-bottom p-3 d-flex align-items-start gap-2">
                                    <a href="${n.link ?? '#'}" class="text-decoration-none flex-grow-1 notification-item" data-id="${n.id}">
                                        <div class="fw-bold small mb-1 text-primary text-wrap">${n.title}</div>
                                        <div class="small text-muted text-wrap">${n.message}</div>
                                        <div class="mt-1" style="font-size:.7rem;color:#adb5bd;">${new Date(n.created_at).toLocaleString()}</div>
                                    </a>
                                </div>
                            `);
                        });
                    } else {
                        containers.append(
                            '<div class="p-4 text-center text-muted small">' + @json(__('No new notifications.')) + '</div>'
                        );
                    }
                },
                error: function(xhr) { console.error('Notifications error:', xhr); }
            });
        }

        fetchNotifications();
        setInterval(fetchNotifications, 60000);

        $(document).on('click', '.notification-item', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let link = $(this).attr('href');
            $.ajax({
                url: `/api/notifications/${id}/mark-as-read`,
                method: 'POST',
                success: function() {
                    fetchNotifications();
                    if (link && link !== '#') window.location.href = link;
                }
            });
        });
        @endauth
    </script>
    @stack('scripts')
</body>
</html>