<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Laravel') }}</title>

      <!-- Fonts -->
      <link rel="dns-prefetch" href="//fonts.bunny.net">
      <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
  
      <!-- Bootstrap CSS -->
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
      <!-- Font Awesome -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
      <!-- Bootstrap Icons -->
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

      <!-- Toastr CSS -->
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

      <!-- Custom CSS -->
      <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  
      <!-- Inline CSS (same as provided in the second layout) -->
      <style>
        
  
          .wrapper {
              display: flex;
              width: 100%;
              align-items: stretch;
          }
  
          #sidebar {
              min-width: 250px;
              max-width: 250px;
              /* flex + stretch empêchait sticky : hauteur = contenu, plafonnée au viewport */
              align-self: flex-start;
              position: sticky;
              top: 0;
              min-height: 100vh;
              max-height: 100vh;
              overflow-y: auto;
              overflow-x: hidden;
              background: var(--primary-color);
              color: var(--text-white);
              transition: var(--transition-base);
              box-shadow: var(--shadow-md);
              z-index: 1000;
              overscroll-behavior: contain;
          }
  
          #sidebar.active {
              margin-left: -250px;
          }
  
          #sidebar .sidebar-header {
              padding: 20px;
              background: var(--primary-dark);
              text-align: center;
          }
  
          #sidebar .sidebar-header a {
              font-size: 1.25rem;
              font-weight: bold;
          }
  
          #sidebar ul.components {
              padding: 20px 0;
          }
  
          #sidebar ul li a {
              padding: 15px 20px;
              font-size: 1.1em;
              display: block;
              color: rgba(255, 255, 255, 0.8);
              border-left: 4px solid transparent;
              transition: var(--transition-base);
          }
  
          #sidebar ul li a:hover {
              color: var(--text-white);
              background: var(--primary-light);
              border-left-color: var(--accent-color);
          }
          
          #sidebar ul li.active > a, a[aria-expanded="true"] {
              color: var(--accent-color);
              background: var(--primary-light);
              font-weight: 600;
          }
  
          #sidebar ul li a i {
              margin-right: 10px;
          }
  
          #content {
              width: 100%;
              min-height: 100vh;
              transition: var(--transition-base);
          }
  
          #content .navbar {
              background: var(--text-white);
              box-shadow: var(--shadow-sm);
              position: sticky;
              top: 0;
              /* Au-dessus du <main> / cartes ; sous modales Bootstrap (~1055). Sidebar reste à z-index 1000. */
              z-index: 1040;
          }
          
          #sidebarCollapse {
              background-color: var(--primary-color);
              border-color: var(--primary-color);
              transition: var(--transition-base);
          }
          
          #sidebarCollapse:hover {
              background-color: var(--primary-light);
              border-color: var(--primary-light);
          }
          
          #navbarDropdown {
              color: var(--text-primary);
          }
  
          @media (max-width: 768px) {
              #sidebar {
                  margin-left: -250px;
              }
              #sidebar.active {
                  margin-left: 0;
              }
          }
      </style>
  
      @stack('styles')
</head>
<body>
    <div id="app" class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <a class="navbar-brand text-white" href="#">
                    <i class="fas fa-user-cog"></i> {{ ucfirst(Auth::user()->role) }} Panel
                </a>
            </div>
            @includeIf('partials.sidebar-' . Auth::user()->role)
        </nav>

        <!-- Content -->
        <div id="content">
            @if(session()->has('impersonating'))
                <div class="alert alert-warning mb-0 text-center">
                    You are currently impersonating a user. 
                    <a href="{{ route('users.stop-impersonating') }}">{{ __('Stop Impersonating') }}</a>
                </div>
            @endif
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-dark">
                        <i class="fas fa-align-left"></i>
                        <span class="ms-2 d-none d-md-inline">{{ __('Menu') }}</span>
                    </button>
                    <ul class="navbar-nav ms-auto">
                        <!-- Notifications Dropdown -->
                        <li class="nav-item dropdown me-2">
                            <a id="navbarDropdownNotifications" class="nav-link dropdown-toggle position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="bi bi-bell"></i>
                                <span class="badge bg-danger d-none" id="unread-notifications-count" style="position: absolute; top: 0px; right: 0px; padding: 3px 6px; border-radius: 50%; font-size: 0.6rem;">0</span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="navbarDropdownNotifications" id="notifications-dropdown-menu" style="width: 300px; max-height: 400px; overflow-y: auto;">
                                <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center bg-light">
                                    <h6 class="dropdown-header p-0 mb-0 fw-bold">{{ __('Notifications') }}</h6>
                                    <button class="btn btn-link btn-sm p-0 d-none text-decoration-none" id="mark-all-read-btn" title="{{ __('Marquer tout comme lu') }}">
                                        <i class="bi bi-check2-all me-1"></i><small>{{ __('Tout lire') }}</small>
                                    </button>
                                </div>
                                <div id="notifications-list">
                                    <div class="p-3 text-center">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-2 border-top text-center bg-light">
                                    <a class="dropdown-item small text-primary fw-bold" href="{{ route('admin.notifications.index') }}">{{ __('Voir toutes les notifications') }}</a>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="fas fa-user me-2"></i> {{ __('Profile') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('profile.notifications.edit') }}">
                                    <i class="fas fa-bell me-2"></i> {{ __('Notifications') }}
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="p-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('warning') }}
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

                <div >
                    <h1 class="h2">@yield('header')</h1>
                </div>
                @yield('content')
            </main>
        </div>
    </div>

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        // Setup CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('sidebarCollapse').addEventListener('click', function () {
                document.getElementById('sidebar').classList.toggle('active');
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
        // AJAX scripts for favorites, etc. can go here or be pushed via @stack('scripts')
    </script>
    @stack('scripts')
</body>
</html>
