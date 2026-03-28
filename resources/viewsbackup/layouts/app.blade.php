<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Google reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm" style="z-index: 100">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.jpg') }}" alt="{{ __('Logo') }}" height="32" class="me-2">
                </a>
                
                <!-- Right Side - Notifications & Profile -->
                <div class="d-flex align-items-center order-md-last gap-2">
                    @auth
                        <!-- Notifications Dropdown Amélioré -->
                        <div class="dropdown">
                            <a class="nav-link position-relative p-2" href="#" 
                               role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell fs-5"></i>
                                <span class="badge bg-danger position-absolute top-0 start-100 translate-middle d-none rounded-pill" 
                                      id="unread-notifications-count"
                                      style="font-size: 0.65rem; padding: 4px 7px; min-width: 18px; text-align: center;">
                                    0
                                </span>
                            </a>
                            
                            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0" 
                                 id="notifications-dropdown-menu" 
                                 style="width: 360px; max-height: 75vh; overflow: hidden; border-radius: 12px; z-index: 1060;">
                                
                                <!-- Header fixe -->
                                <div class="dropdown-header d-flex justify-content-between align-items-center border-bottom px-3 py-3 bg-light">
                                    <h6 class="mb-0 fw-semibold text-dark">{{ __('Notifications') }}</h6>
                                    <button type="button" id="mark-all-read" 
                                            class="btn btn-sm btn-link text-primary text-decoration-none p-0 fw-medium">
                                        {{ __('Tout marquer lu') }}
                                    </button>
                                </div>

                                <!-- Zone de scroll -->
                                <div id="notifications-list" 
                                     style="max-height: calc(75vh - 118px); overflow-y: auto; overscroll-behavior: contain;">
                                    <!-- Notifications chargées par JS -->
                                </div>

                                <!-- Footer fixe -->
                                <div class="border-top bg-light p-2">
                                    <a href="{{ route('notifications.index') ?? '#' }}" 
                                       class="btn btn-outline-primary btn-sm w-100">
                                        {{ __('Voir toutes les notifications') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" 
                               href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="{{ Auth::user()->avatar_url }}" 
                                     alt="{{ Auth::user()->name }}" 
                                     class="rounded-circle me-1" 
                                     style="width: 28px; height: 28px; object-fit: cover;">
                                <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                            </a>
                            @include('partials.user-dropdown')
                        </div>
                    @endauth

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" 
                            aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('library.index') }}">{{ __('Bibliothèque') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('subscription.plans') }}">{{ __('Abonnements') }}</a>
                        </li>

                        @auth
                            <li class="nav-item">
                                <a class="nav-link fw-bold" href="
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

                    <!-- Right Side Language -->
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item dropdown">
                            <a id="navbarDropdownLang" class="nav-link dropdown-toggle" 
                               href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bi bi-translate"></i> {{ strtoupper(app()->getLocale()) }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownLang">
                                <a class="dropdown-item @if(app()->getLocale() == 'en') active @endif" 
                                   href="{{ route('change.language', 'en') }}">English</a>
                                <a class="dropdown-item @if(app()->getLocale() == 'fr') active @endif" 
                                   href="{{ route('change.language', 'fr') }}">Français</a>
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
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            <div class="container mt-3">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
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

        <footer class="bg-light py-4 mt-auto">
            <div class="container text-center">
                <p class="mb-2 text-muted small">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. {{ __('All rights reserved.') }}</p>
                <div class="dropup d-inline-block">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-translate"></i> {{ __('Language') }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('change.language', 'en') }}">English</a></li>
                        <li><a class="dropdown-item" href="{{ route('change.language', 'fr') }}">Français</a></li>
                    </ul>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $.ajaxSetup({ 
            headers: { 
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
            } 
        });

        @auth
        function fetchNotifications() {
            $.get("{{ route('api.notifications.index') }}", function(res) {
                const countEl = $('#unread-notifications-count');
                countEl.text(res.unread_count || 0);
                countEl.toggleClass('d-none', (res.unread_count || 0) === 0);

                const list = $('#notifications-list');
                list.empty();

                if (res.notifications && res.notifications.length > 0) {
                    res.notifications.forEach(n => {
                        const isUnread = !n.read_at;
                        const html = `
                            <a href="${n.link ?? '#'}" 
                               class="notification-item ${isUnread ? 'unread' : ''}" 
                               data-id="${n.id}">
                                <strong>${n.title}</strong>
                                <small class="text-muted d-block mt-1">${n.message}</small>
                                ${n.created_at ? `<small class="text-muted d-block mt-2">${n.created_at}</small>` : ''}
                            </a>`;
                        list.append(html);
                    });
                } else {
                    list.append(`
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-bell-slash fs-1 mb-3 opacity-50"></i>
                            <p class="mb-0 small">{{ __('Aucune notification pour le moment') }}</p>
                        </div>
                    `);
                }
            });
        }

        // Charger les notifications au démarrage
        fetchNotifications();
        setInterval(fetchNotifications, 60000);

        // Marquer une notification comme lue
        $(document).on('click', '.notification-item', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const link = $(this).attr('href');

            $.post(`/api/notifications/${id}/mark-as-read`).done(() => {
                if (link && link !== '#') {
                    window.location.href = link;
                } else {
                    fetchNotifications();
                }
            });
        });

        // Marquer toutes les notifications comme lues
        $(document).on('click', '#mark-all-read', function() {
            $.post("{{ route('api.notifications.mark-all-read') ?? '/api/notifications/mark-all-read' }}")
             .done(() => {
                 fetchNotifications();
                 toastr.success("{{ __('Toutes les notifications ont été marquées comme lues') }}");
             });
        });
        @endauth

        // AJAX Favorites (inchangé)
        $(document).on('submit', '.favorite-form', function(e) {
            e.preventDefault();
            let form = $(this);
            let btn = form.find('button[type="submit"]');
            $.post(form.attr('action'), form.serialize()).done(function(res) {
                toastr.success(res.message);
                if (res.status === 'favorited') {
                    btn.removeClass('btn-outline-danger').addClass('btn-danger')
                       .find('i').removeClass('far').addClass('fas');
                } else {
                    btn.removeClass('btn-danger').addClass('btn-outline-danger')
                       .find('i').removeClass('fas').addClass('far');
                    if (form.closest('.book-card-col').length) {
                        form.closest('.book-card-col').fadeOut();
                    }
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>