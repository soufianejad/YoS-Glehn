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
                
                <!-- Right Side - Always Visible Mobile (Notifications & Profile) -->
                <div class="d-flex align-items-center order-md-last gap-2">
                    @auth
                        <div class="dropdown">
                            <a class="nav-link position-relative p-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" v-pre>
                                <i class="bi bi-bell"></i>
                                <span class="badge bg-danger d-none" id="unread-notifications-count" style="position: absolute; top: 0; right: 0; padding: 3px 6px; border-radius: 50%; font-size: 0.6rem;">0</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow-sm" id="notifications-dropdown-menu" style="width: 280px; max-height: 350px; overflow-y: auto;">
                                <h6 class="dropdown-header">{{ __('Notifications') }}</h6>
                                <div id="notifications-list">
                                    <!-- Notifications load here -->
                                </div>
                            </div>
                        </div>

                        <div class="dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="rounded-circle me-1" style="width: 28px; height: 28px; object-fit: cover;">
                                <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                            </a>
                            @include('partials.user-dropdown')
                        </div>
                    @endauth

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>

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
                        <li class="nav-item dropdown">
                            <a id="navbarDropdownLang" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="bi bi-translate"></i> {{ strtoupper(app()->getLocale()) }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownLang">
                                <a class="dropdown-item @if(app()->getLocale() == 'en') active @endif" href="{{ route('change.language', 'en') }}">English</a>
                                <a class="dropdown-item @if(app()->getLocale() == 'fr') active @endif" href="{{ route('change.language', 'fr') }}">Français</a>
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
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

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
                        list.append(`<a class="dropdown-item border-bottom notification-item" href="${n.link ?? '#'}" data-id="${n.id}"><strong>${n.title}</strong><br><small class="text-muted">${n.message}</small></a>`);
                    });
                    list.append('<a class="dropdown-item text-center small py-2" href="#">{{ __("Tout voir") }}</a>');
                } else {
                    list.append('<span class="dropdown-item text-center text-muted small py-3">{{ __("Aucune notification") }}</span>');
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

        // AJAX Favorites
        $(document).on('submit', '.favorite-form', function(e) {
            e.preventDefault();
            let form = $(this);
            let btn = form.find('button[type="submit"]');
            $.post(form.attr('action'), form.serialize()).done(function(res) {
                toastr.success(res.message);
                if (res.status === 'favorited') {
                    btn.removeClass('btn-outline-danger').addClass('btn-danger').find('i').removeClass('far').addClass('fas');
                } else {
                    btn.removeClass('btn-danger').addClass('btn-outline-danger').find('i').removeClass('fas').addClass('far');
                    if (form.closest('.book-card-col').length) form.closest('.book-card-col').fadeOut();
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
