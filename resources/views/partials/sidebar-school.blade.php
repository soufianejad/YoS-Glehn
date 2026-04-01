<ul class="list-unstyled components">
    <!-- General -->
    <li>
        <a href="#generalSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('school.dashboard*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Général') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('school.dashboard*') ? 'show' : '' }}" id="generalSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('school.dashboard*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> {{ __('Tableau de Bord') }}
                </a>
            </li>
        </ul>
    </li>

    <!-- Management -->
    <li class="mt-1">
        <a href="#managementSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('school.students*') || request()->routeIs('school.teachers*') || request()->routeIs('school.parents*') || request()->routeIs('school.classes*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Gestion') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('school.students*') || request()->routeIs('school.teachers*') || request()->routeIs('school.parents*') || request()->routeIs('school.classes*') ? 'show' : '' }}" id="managementSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('school.students*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.students.index') }}">
                    <i class="fas fa-user-graduate"></i> {{ __('Étudiants') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('school.teachers*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.teachers.index') }}">
                    <i class="fas fa-chalkboard-teacher"></i> {{ __('Enseignants') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('school.parents*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.parents.index') }}">
                    <i class="fas fa-user-shield"></i> {{ __('Parents') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('school.classes*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.classes.index') }}">
                    <i class="fas fa-school"></i> {{ __('Classes') }}
                </a>
            </li>
        </ul>
    </li>

    <!-- Content -->
    <li class="mt-1">
        <a href="#contentSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('school.books.assignments*') || request()->routeIs('school.announcements*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Contenu') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('school.books.assignments*') || request()->routeIs('school.announcements*') ? 'show' : '' }}" id="contentSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('school.books.assignments*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.books.assignments.index') }}">
                    <i class="fas fa-book"></i> {{ __('Affectations de Livres') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('school.announcements*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.announcements.index') }}">
                    <i class="fas fa-bullhorn"></i> {{ __('Annonces') }}
                </a>
            </li>
        </ul>
    </li>

    <!-- Reports -->
    <li class="mt-1">
        <a href="#reportsSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('school.statistics') || request()->routeIs('school.progress-report') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Rapports') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('school.statistics') || request()->routeIs('school.progress-report') ? 'show' : '' }}" id="reportsSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('school.statistics') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.statistics') }}">
                    <i class="fas fa-chart-pie"></i> {{ __('Statistiques') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('school.progress-report') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.progress-report') }}">
                    <i class="fas fa-clipboard-list"></i> {{ __('Rapport de Progrès') }}
                </a>
            </li>
        </ul>
    </li>

    <!-- Configuration -->
    <li class="mt-1">
        <a href="#configSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('school.settings*') || request()->routeIs('school.qrcode*') || request()->routeIs('school.subscription*') || request()->routeIs('subscription.index') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Configuration') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('school.settings*') || request()->routeIs('school.qrcode*') || request()->routeIs('school.subscription*') || request()->routeIs('subscription.index') ? 'show' : '' }}" id="configSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('school.settings*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.settings') }}">
                    <i class="fas fa-cog"></i> {{ __('Paramètres') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('school.qrcode*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.qrcode') }}">
                    <i class="fas fa-qrcode"></i> {{ __('Inscription par QR Code') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('school.subscription*') || request()->routeIs('subscription.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.subscription.index') ?? route('subscription.index') }}">
                    <i class="fas fa-credit-card"></i> {{ __('Abonnement') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('school.profile*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.settings') }}">
                    <i class="fas fa-user"></i> {{ __('Profil') }}
                </a>
            </li>
        </ul>
    </li>

    <!-- Communication -->
    <li class="mt-1">
        <a href="#communicationSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('messaging.index') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Communication') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('messaging.index') ? 'show' : '' }}" id="communicationSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('messaging.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('messaging.index') }}">
                    <i class="fas fa-comments"></i> {{ __('Messagerie') }}
                    @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                        <span class="badge bg-danger float-right">{{ $unreadMessagesCount }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </li>
</ul>

<style>
    .components .nav-link {
        padding-left: 2.5rem !important;
    }

    .components > li > a.dropdown-toggle.sidebar-heading {
        color: rgba(255, 255, 255, 0.6);
    }

    .components > li > a.dropdown-toggle.sidebar-heading:hover,
    .components > li > a.dropdown-toggle.sidebar-heading[aria-expanded="true"] {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.1);
    }
</style>