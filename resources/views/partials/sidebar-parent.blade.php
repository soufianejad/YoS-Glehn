<ul class="list-unstyled components">
    <!-- General -->
    <li>
        <a href="#generalSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('parent.dashboard*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Général') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('parent.dashboard*') ? 'show' : '' }}" id="generalSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('parent.dashboard*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('parent.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> {{ __('Tableau de Bord') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('parent.dashboard*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('parent.dashboard') }}">
                    <i class="fas fa-child"></i> {{ __('Mes Enfants') }}
                </a>
            </li>
        </ul>
    </li>

    <!-- My Account -->
    <li class="mt-1">
        <a href="#accountSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('reader.profile') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Mon Compte') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('reader.profile') ? 'show' : '' }}" id="accountSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.profile') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.profile') }}"><i class="fas fa-user-cog"></i> {{ __("Mon Profil") }}</a>
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
                    <i class="fas fa-comments"></i> {{ __("Messagerie") }}
                    @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                        <span class="badge bg-danger float-end">{{ $unreadMessagesCount }}</span>
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
