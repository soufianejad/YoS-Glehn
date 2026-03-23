<ul class="list-unstyled components">
    <!-- General -->
    <li>
        <a href="#generalSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('reader.dashboard') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none {{ request()->routeIs('reader.dashboard') ? 'collapsed' : 'collapsed' }}">
            {{ __('Général') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('reader.dashboard') ? 'show' : '' }}" id="generalSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> {{ __('Tableau de Bord') }}
                </a>
            </li>
        </ul>
    </li>

    <!-- My Library -->
    <li class="mt-1">
        <a href="#librarySubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('reader.library') || request()->routeIs('reader.favorites') || request()->routeIs('bookmarks.showAll') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Ma Bibliothèque') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('reader.library') || request()->routeIs('reader.favorites') || request()->routeIs('bookmarks.showAll') ? 'show' : '' }}" id="librarySubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.library') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.library') }}"><i class="fas fa-book-open"></i> {{ __('Ma Bibliothèque') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.favorites') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.favorites') }}"><i class="fas fa-heart"></i> {{ __('Mes Favoris') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('bookmarks.showAll') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('bookmarks.showAll') }}"><i class="fas fa-bookmark"></i> {{ __('Mes Marque-pages') }}</a>
            </li>
        </ul>
    </li>

    <!-- My Activity -->
    <li class="mt-1">
        <a href="#activitySubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('reader.quizzes') || request()->routeIs('reader.badges') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Mon Activité') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('reader.quizzes') || request()->routeIs('reader.badges') ? 'show' : '' }}" id="activitySubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.quizzes') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.quizzes') }}"><i class="fas fa-question-circle"></i> {{ __('Mes Quiz') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.badges') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.badges') }}"><i class="fas fa-award"></i> {{ __('Mes Badges') }}</a>
            </li>
        </ul>
    </li>

    <!-- My Account -->
    <li class="mt-1">
        <a href="#accountSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('reader.profile') || request()->routeIs('reader.subscription') || request()->routeIs('reader.payments') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Mon Compte') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('reader.profile') || request()->routeIs('reader.subscription') || request()->routeIs('reader.payments') ? 'show' : '' }}" id="accountSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.profile') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.profile') }}"><i class="fas fa-user"></i> {{ __('Mon Profil') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.subscription') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.subscription') }}"><i class="fas fa-credit-card"></i> {{ __('Mon Abonnement') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.payments') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.payments') }}"><i class="fas fa-money-bill-wave"></i> {{ __('Mes Paiements') }}</a>
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