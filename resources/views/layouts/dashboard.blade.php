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
              min-height: 100vh;
              background: var(--primary-color);
              color: var(--text-white);
              transition: var(--transition-base);
              box-shadow: var(--shadow-md);
              z-index: 1000;
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
                    <a href="{{ route('users.stop-impersonating') }}">Stop Impersonating</a>
                </div>
            @endif
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-dark">
                        <i class="fas fa-align-left"></i>
                        <span class="ms-2 d-none d-md-inline">{{ __('Menu') }}</span>
                    </button>
                    <ul class="navbar-nav ms-auto">
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
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
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
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('sidebarCollapse').addEventListener('click', function () {
                document.getElementById('sidebar').classList.toggle('active');
            });
        });
        // AJAX scripts for favorites, etc. can go here or be pushed via @stack('scripts')
    </script>
    @stack('scripts')
</body>
</html>
