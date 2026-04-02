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

    <style>
        /* ── Dropdowns desktop ── */
        .navbar .dropdown-menu {
            position: absolute !important;
            top: 100%;
            right: 0;
            left: auto;
            z-index: 1050;
        }

        /* ── Mobile collapse ── */
        @media (max-width: 767.98px) {
            /* Collapse dans le flux, pas en absolute */
            .navbar-collapse {
                border-top: 1px solid rgba(0,0,0,.08);
                padding: .25rem 0 .5rem;
            }
            /* Liens empilés verticalement */
            #navbarSupportedContent .navbar-nav {
                flex-direction: column !important;
                width: 100%;
            }
            #navbarSupportedContent .nav-link {
                padding: .5rem 1rem;
            }
            /* Dropdowns inline sur mobile */
            #navbarSupportedContent .dropdown-menu {
                position: static !important;
                box-shadow: none !important;
                border: none !important;
                background: #f8f9fa;
                padding-left: 1rem;
            }
            /* Avatar sur mobile : afficher le nom */
            #navbarSupportedContent .d-none.d-lg-inline {
                display: inline !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div id="app" class="d-flex flex-column min-vh-100">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm" style="z-index: 100">
            <div class="container">

                <!-- Logo (toujours visible) -->
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.jpg') }}" alt="{{ __('Logo') }}" height="32" class="me-2">
                </a>

                <!-- Cloche mobile (visible uniquement sur mobile, hors collapse) -->
                @auth
                <div class="d-flex d-md-none align-items-center me-2">
                    <div class="dropdown">
                        <a class="nav-link position-relative p-2"
                           href="#" role="button"
                           data-bs-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-bell fs-5"></i>
                            <span class="badge bg-danger d-none unread-notifications-count"
                                  style="position:absolute;top:0;right:0;padding:3px 6px;border-radius:50%;font-size:.6rem;">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow-sm border-0"
                             style="width:280px;max-height:400px;overflow-y:auto;">
                            <h6 class="dropdown-header">{{ __('Notifications') }}</h6>
                            <div class="notifications-list-container"></div>
                        </div>
                    </div>
                </div>
                @endauth

                <!-- Toggler mobile -->
                <button class="navbar-toggler border-0" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent"
                        aria-expanded="false"
                        aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Collapse : liens gauche + éléments droite -->
                <div class="collapse navbar-collapse" id="navbarSupportedContent">

                    <!-- GAUCHE : liens principaux -->
                    <ul class="navbar-nav me-auto">
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

                    <!-- DROITE : langue, cloche desktop, avatar -->
                    <ul class="navbar-nav ms-auto align-items-md-center">

                        <!-- Langue -->
                        <li class="nav-item dropdown me-md-2">
                            <a id="navbarDropdownLang" class="nav-link dropdown-toggle"
                               href="#" role="button"
                               data-bs-toggle="dropdown"
                               aria-haspopup="true" aria-expanded="false">
                                <i class="bi bi-translate"></i> {{ strtoupper(app()->getLocale()) }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownLang">
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
                                <a class="btn btn-primary btn-sm ms-md-2" href="{{ route('register') }}">{{ __('Register') }}</a>
                            </li>
                            @endif
                        @else
                            <!-- Cloche desktop uniquement -->
                            <li class="nav-item dropdown d-none d-md-flex align-items-center me-2">
                                <a id="navbarDropdownNotifications"
                                   class="nav-link position-relative p-2"
                                   href="#" role="button"
                                   data-bs-toggle="dropdown"
                                   aria-haspopup="true" aria-expanded="false">
                                    <i class="bi bi-bell fs-5"></i>
                                    <span class="badge bg-danger d-none unread-notifications-count"
                                          style="position:absolute;top:0;right:0;padding:3px 6px;border-radius:50%;font-size:.6rem;">0</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2"
                                     aria-labelledby="navbarDropdownNotifications"
                                     style="width:300px;max-height:400px;overflow-y:auto;">
                                    <h6 class="dropdown-header bg-light py-2">{{ __('Notifications') }}</h6>
                                    <div class="notifications-list-container"></div>
                                    <div class="dropdown-divider mb-0"></div>
                                    <a class="dropdown-item text-center small py-2" href="#">{{ __('View all notifications') }}</a>
                                </div>
                            </li>

                            <!-- Avatar + dropdown utilisateur -->
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown"
                                   class="nav-link dropdown-toggle d-flex align-items-center"
                                   href="#" role="button"
                                   data-bs-toggle="dropdown"
                                   aria-haspopup="true" aria-expanded="false">
                                    <img src="{{ Auth::user()->avatar_url }}"
                                         alt="{{ Auth::user()->name }}"
                                         class="rounded-circle me-1"
                                         style="width:30px;height:30px;object-fit:cover;">
                                    <span>{{ Auth::user()->name }}</span>
                                </a>
                                @include('partials.user-dropdown')
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="flex-grow-1">
            @yield('content')
        </main>

        <footer class="bg-light py-4 mt-auto border-top">
            <div class="container text-center">
                <p class="mb-2 text-muted small">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. {{ __('All rights reserved.') }}</p>
                <div class="dropup d-inline-block">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle"
                            type="button"
                            id="dropdownLanguageFooter"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <i class="bi bi-translate me-1"></i> {{ __('Language') }}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownLanguageFooter">
                        <li><a class="dropdown-item @if(app()->getLocale() == 'en') active @endif" href="{{ route('change.language', 'en') }}">{{ __('English') }}</a></li>
                        <li><a class="dropdown-item @if(app()->getLocale() == 'fr') active @endif" href="{{ route('change.language', 'fr') }}">{{ __('French') }}</a></li>
                    </ul>
                </div>
            </div>
        </footer>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        // Setup CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('submit', '.favorite-form', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var button = form.find('button[type="submit"]');
            var icon = button.find('i');
            var buttonTextSpan = button.find('#favorite-button-text');

            $.ajax({
                type: 'POST',
                url: url,
                data: form.serialize(),
                success: function(response) {
                    toastr.success(response.message);
                    if (response.status === 'favorited') {
                        icon.removeClass('far fa-heart').addClass('fas fa-heart');
                        button.removeClass('btn-outline-danger').addClass('btn-danger');
                        if (buttonTextSpan.length) {
                            buttonTextSpan.text(@json(__('Retirer des favoris')));
                        } else {
                            button.html(
                                '<i class="fas fa-heart me-2"></i> ' +
                                @json(__('Retirer des favoris'))
                            );
                        }
                    } else {
                        icon.removeClass('fas fa-heart').addClass('far fa-heart');
                        button.removeClass('btn-danger').addClass('btn-outline-danger');
                        if (buttonTextSpan.length) {
                            buttonTextSpan.text(@json(__('Ajouter aux favoris')));
                        } else {
                            button.html(
                                '<i class="far fa-heart me-2"></i> ' +
                                @json(__('Ajouter aux favoris'))
                            );
                        }
                        if (form.closest('.book-card-col').length) {
                            form.closest('.book-card-col').fadeOut();
                        }
                    }
                },
                error: function(xhr) {
                    toastr.error(@json(__('An error occurred. Please try again.')));
                }
            });
        });

        // Notification System
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

                    let notificationsContainers = $('.notifications-list-container');
                    notificationsContainers.empty();

                    if (response.notifications.length > 0) {
                        response.notifications.forEach(function(n) {
                            notificationsContainers.append(`
                                <div class="dropdown-item border-bottom p-3 d-flex align-items-start gap-2">
                                    <a href="${n.link ?? '#'}" class="text-decoration-none flex-grow-1 notification-item" data-id="${n.id}">
                                        <div class="fw-bold small mb-1 text-primary text-wrap">${n.title}</div>
                                        <div class="small text-muted text-wrap">${n.message}</div>
                                        <div class="mt-1" style="font-size: 0.7rem; color: #adb5bd;">${new Date(n.created_at).toLocaleString()}</div>
                                    </a>
                                </div>
                            `);
                        });
                    } else {
                        notificationsContainers.append(
                            '<div class="p-4 text-center text-muted small">' +
                            @json(__('No new notifications.')) +
                            '</div>'
                        );
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching notifications:', xhr);
                }
            });
        }

        // Initial load
        fetchNotifications();

        // Refresh every 60s
        setInterval(fetchNotifications, 60000);

        // Mark as read and navigate
        $(document).on('click', '.notification-item', function(e) {
            e.preventDefault();
            let notificationId = $(this).data('id');
            let notificationLink = $(this).attr('href');

            $.ajax({
                url: `/api/notifications/${notificationId}/mark-as-read`,
                method: 'POST',
                success: function(response) {
                    fetchNotifications();
                    if (notificationLink && notificationLink !== '#') {
                        window.location.href = notificationLink;
                    }
                }
            });
        });
        @endauth
    </script>
    @stack('scripts')
</body>
</html>