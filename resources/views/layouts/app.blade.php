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

    @stack('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm" style="z-index: 100">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.jpg') }}" alt="{{ __('Logo') }}" height="32" class="me-2">
                    {{-- <span>{{ config('app.name', 'Laravel') }}</span> --}}
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
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

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto align-items-center">
                        <!-- Language Switcher -->
                        <li class="nav-item dropdown">
                            <a id="navbarDropdownLang" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bi bi-translate"></i> {{ strtoupper(app()->getLocale()) }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownLang">
                                <a class="dropdown-item @if(app()->getLocale() == 'en') active @endif" href="{{ route('change.language', 'en') }}">
                                    {{ __('English') }}
                                </a>
                                <a class="dropdown-item @if(app()->getLocale() == 'fr') active @endif" href="{{ route('change.language', 'fr') }}">
                                    {{ __('French') }}
                                </a>
                            </div>
                        </li>

                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="btn btn-primary ms-2" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdownNotifications" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="bi bi-bell"></i>
                                    <span class="badge bg-danger" id="unread-notifications-count" style="position: absolute; top: 5px; right: 5px; padding: 5px 8px; border-radius: 50%;">0</span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownNotifications" id="notifications-dropdown-menu">
                                    <h6 class="dropdown-header">{{ __('Notifications') }}</h6>
                                    <div id="notifications-list">
                                        <!-- Notifications will be loaded here -->
                                        <a class="dropdown-item text-center" href="#">{{ __('View all notifications') }}</a>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="rounded-circle me-1" style="width: 30px; height: 30px; object-fit: cover;">
                                    {{ Auth::user()->name }}
                                </a>

                                @include('partials.user-dropdown')
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>

        <footer class="bg-light py-4 mt-auto">
            <div class="container text-center">
                <p class="mb-2">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. {{ __('All rights reserved.') }}</p>
                <div class="dropup d-inline-block">
                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownLanguageFooter" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-translate"></i> {{ __('Language') }}
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

        document.addEventListener("DOMContentLoaded", function() {
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl)
            })
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
                    } else { // unfavorited
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
                    let countEl = $('#unread-notifications-count');
                    let markAllBtn = $('#mark-all-read-btn');
                    countEl.text(response.unread_count);

                    if (response.unread_count > 0) {
                        countEl.removeClass('d-none').show();
                        markAllBtn.removeClass('d-none');
                    } else {
                        countEl.addClass('d-none').hide();
                        markAllBtn.addClass('d-none');
                    }

                    let notificationsList = $('#notifications-list');
                    notificationsList.empty();

                    if (response.notifications.length > 0) {
                        response.notifications.forEach(function(n) {
                            notificationsList.append(`
                                <div class="dropdown-item border-bottom p-3 d-flex align-items-start gap-2">
                                    <a href="${n.link ?? '#'}" class="text-decoration-none flex-grow-1 notification-item" data-id="${n.id}">
                                        <div class="fw-bold small mb-1 text-primary text-wrap">${n.title}</div>
                                        <div class="small text-muted text-wrap">${n.message}</div>
                                        <div class="mt-1" style="font-size: 0.7rem; color: #adb5bd;">${new Date(n.created_at).toLocaleString()}</div>
                                    </a>
                                    <button class="btn btn-link btn-sm p-0 text-muted mark-single-read" data-id="${n.id}" title="{{ __('Marquer comme lu') }}">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                </div>
                            `);
                        });
                    } else {
                        notificationsList.append(
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

        // Mark single as read
        $(document).on('click', '.mark-single-read', function(e) {
            e.stopPropagation();
            let id = $(this).data('id');
            $.post(`/api/notifications/${id}/mark-as-read`).done(() => {
                fetchNotifications();
            });
        });

        // Mark all as read
        $(document).on('click', '#mark-all-read-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $.post("{{ route('api.notifications.markAllRead') }}").done(() => {
                fetchNotifications();
                toastr.success("{{ __('Toutes les notifications ont été marquées comme lues') }}");
            });
        });

        // Mark as read and navigate
        $(document).on('click', '.notification-item', function(e) {
            e.preventDefault();
            let notificationId = $(this).data('id');
            let notificationLink = $(this).attr('href');

            $.ajax({
                url: `/api/notifications/${notificationId}/mark-as-read`,
                method: 'POST',
                data: {
                    _token: @json(csrf_token())
                },
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