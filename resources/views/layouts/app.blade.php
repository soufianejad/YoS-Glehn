<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #0d6efd;
            --nav-height: 70px;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            padding-top: var(--nav-height);
        }
        .navbar {
            height: var(--nav-height);
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
        }
        .navbar-brand img {
            height: 38px;
            transition: transform 0.3s ease;
        }
        .navbar-brand:hover img {
            transform: scale(1.05);
        }
        .nav-link {
            font-weight: 500;
            color: #475569 !important;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .nav-link:hover {
            color: var(--primary-color) !important;
            background: rgba(13, 110, 253, 0.05);
        }
        .nav-link.active {
            color: var(--primary-color) !important;
            font-weight: 600;
        }
        .navbar-toggler {
            border: none;
            padding: 0;
        }
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        /* Mobile Improvements */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: white;
                margin-top: 10px;
                padding: 1rem;
                border-radius: 12px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            }
            .nav-auth-mobile {
                display: flex !important;
                align-items: center;
                gap: 15px;
            }
        }
        
        .notification-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            padding: 4px 6px;
            border-radius: 50%;
            font-size: 0.65rem;
            border: 2px solid white;
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #e2e8f0;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 0.5rem;
        }
        .dropdown-item {
            border-radius: 8px;
            padding: 0.6rem 1rem;
            font-weight: 500;
        }
        
        footer {
            background: white;
            border-top: 1px solid #e2e8f0;
            padding: 3rem 0;
        }
    </style>

    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.jpg') }}" alt="{{ config('app.name') }}">
                </a>
                
                <!-- Right Side - Always Visible Mobile -->
                <div class="d-flex align-items-center order-lg-last gap-2 gap-md-3">
                    @auth
                        <!-- Notifications -->
                        <div class="dropdown">
                            <a class="nav-link position-relative p-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell fs-5"></i>
                                <span class="badge bg-danger notification-badge d-none" id="unread-notifications-count">0</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end p-0" style="width: 300px; max-height: 400px; overflow-y: auto;">
                                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold">{{ __('Notifications') }}</h6>
                                    {{-- <a href="#" class="small text-decoration-none">{{ __('Marquer tout comme lu') }}</a> --}}
                                </div>
                                <div id="notifications-list" class="py-1">
                                    <div class="text-center py-3 text-muted small">{{ __('Chargement...') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- User Profile -->
                        <div class="dropdown">
                            <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="user-avatar shadow-sm">
                                <span class="ms-2 d-none d-md-inline fw-semibold text-dark">{{ Auth::user()->name }}</span>
                            </a>
                            @include('partials.user-dropdown')
                        </div>
                    @else
                        <div class="d-none d-sm-flex gap-2">
                            <a href="{{ route('login') }}" class="btn btn-link text-decoration-none fw-semibold text-dark">{{ __('Connexion') }}</a>
                            <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-4">{{ __('Inscription') }}</a>
                        </div>
                    @endauth

                    <!-- Toggler -->
                    <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false">
                        <i class="bi bi-list fs-2 text-dark"></i>
                    </button>
                </div>

                <!-- Main Menu -->
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('library.*') ? 'active' : '' }}" href="{{ route('library.index') }}">
                                <i class="bi bi-grid me-1"></i>{{ __('Bibliothèque') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('subscription.plans') ? 'active' : '' }}" href="{{ route('subscription.plans') }}">
                                <i class="bi bi-award me-1"></i>{{ __('Abonnements') }}
                            </a>
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
                                ">
                                    <i class="bi bi-speedometer2 me-1"></i>{{ __('Tableau de bord') }}
                                </a>
                            </li>
                        @endauth
                        
                        <!-- Mobile Auth Links (Inside toggler) -->
                        @guest
                            <li class="nav-item d-sm-none mt-3">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill">{{ __('Connexion') }}</a>
                                    <a href="{{ route('register') }}" class="btn btn-primary rounded-pill">{{ __('Inscription') }}</a>
                                </div>
                            </li>
                        @endguest
                    </ul>

                    <!-- Language Switcher - Left/Bottom of mobile menu -->
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" v-pre>
                                <i class="bi bi-translate me-1"></i>{{ strtoupper(app()->getLocale()) }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('change.language', 'en') }}">🇺🇸 English</a></li>
                                <li><a class="dropdown-item {{ app()->getLocale() == 'fr' ? 'active' : '' }}" href="{{ route('change.language', 'fr') }}">🇫🇷 Français</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            <!-- Global Feedback Section -->
            <div class="container pt-3">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            @yield('content')
        </main>

        <footer class="footer mt-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <p class="mb-0 text-muted">&copy; {{ date('Y') }} <strong>{{ config('app.name') }}</strong>. {{ __('Tous droits réservés.') }}</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="dropup d-inline-block">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle rounded-pill px-3" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-translate me-1"></i> {{ __('Langue') }}
                            </button>
                            <ul class="dropdown-menu shadow">
                                <li><a class="dropdown-item" href="{{ route('change.language', 'en') }}">🇺🇸 English</a></li>
                                <li><a class="dropdown-item" href="{{ route('change.language', 'fr') }}">🇫🇷 Français</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- jQuery & JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // AJAX Favorites
        $(document).on('submit', '.favorite-form', function(e) {
            e.preventDefault();
            let form = $(this);
            let button = form.find('button[type="submit"]');
            let icon = button.find('i');

            $.post(form.attr('action'), form.serialize())
                .done(function(res) {
                    toastr.success(res.message);
                    if (res.status === 'favorited') {
                        icon.removeClass('far fa-heart').addClass('fas fa-heart');
                        button.removeClass('btn-outline-danger').addClass('btn-danger');
                    } else {
                        icon.removeClass('fas fa-heart').addClass('far fa-heart');
                        button.removeClass('btn-danger').addClass('btn-outline-danger');
                        if (form.closest('.book-card-col').length) form.closest('.book-card-col').fadeOut();
                    }
                })
                .fail(function() { toastr.error('{{ __('Une erreur est survenue.') }}'); });
        });

        @auth
        function fetchNotifications() {
            $.get("{{ route('api.notifications.index') }}", function(res) {
                let countEl = $('#unread-notifications-count');
                countEl.text(res.unread_count);
                res.unread_count > 0 ? countEl.removeClass('d-none') : countEl.addClass('d-none');

                let list = $('#notifications-list');
                list.empty();
                if (res.notifications.length > 0) {
                    res.notifications.forEach(n => {
                        list.append(`
                            <a class="dropdown-item p-3 border-bottom notification-item" href="${n.link ?? '#'}" data-id="${n.id}">
                                <div class="fw-bold small mb-1 text-primary">${n.title}</div>
                                <div class="small text-muted">${n.message}</div>
                            </a>
                        `);
                    });
                    list.append('<a class="dropdown-item text-center py-2 small fw-bold text-primary" href="#">{{ __("Tout voir") }}</a>');
                } else {
                    list.append('<div class="p-4 text-center text-muted small">{{ __("Aucune notification") }}</div>');
                }
            });
        }
        fetchNotifications();
        setInterval(fetchNotifications, 60000);

        $(document).on('click', '.notification-item', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let link = $(this).attr('href');
            $.post(`/api/notifications/${id}/mark-as-read`).done(() => {
                if (link && link !== '#') window.location.href = link;
                else fetchNotifications();
            });
        });
        @endauth
    </script>
    @stack('scripts')
</body>
</html>
