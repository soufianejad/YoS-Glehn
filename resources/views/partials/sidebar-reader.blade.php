<ul class="list-unstyled components">
    <!-- General -->
    <li>
        <a href="#generalSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('reader.dashboard') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none {{ request()->routeIs('reader.dashboard') ? 'collapsed' : 'collapsed' }}">
            {{ __('General') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('reader.dashboard') ? 'show' : '' }}" id="generalSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> {{ __('Dashboard') }}
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
            {{ __('My library') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('reader.library') || request()->routeIs('reader.favorites') || request()->routeIs('bookmarks.showAll') ? 'show' : '' }}" id="librarySubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.library') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.library') }}"><i class="fas fa-book-open"></i> {{ __('My library') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.favorites') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.favorites') }}"><i class="fas fa-heart"></i> {{ __('My favorites') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('bookmarks.showAll') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('bookmarks.showAll') }}"><i class="fas fa-bookmark"></i> {{ __('My bookmarks') }}</a>
            </li>
        </ul>
    </li>

    <!-- My Activity -->
    <li class="mt-1">
        <a href="#activitySubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('reader.quizzes') || request()->routeIs('reader.badges') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('My activity') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('reader.quizzes') || request()->routeIs('reader.badges') ? 'show' : '' }}" id="activitySubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.quizzes') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.quizzes') }}"><i class="fas fa-question-circle"></i> {{ __('My quizzes') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.badges') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.badges') }}"><i class="fas fa-award"></i> {{ __('My badges') }}</a>
            </li>
        </ul>
    </li>

    <!-- My Account -->
    <li class="mt-1">
        <a href="#accountSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('reader.profile') || request()->routeIs('reader.subscription') || request()->routeIs('reader.payments') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('My account') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('reader.profile') || request()->routeIs('reader.subscription') || request()->routeIs('reader.payments') ? 'show' : '' }}" id="accountSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.profile') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.profile') }}"><i class="fas fa-user"></i> {{ __('My profile') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.subscription') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.subscription') }}"><i class="fas fa-credit-card"></i> {{ __('My subscription') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.payments') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.payments') }}"><i class="fas fa-money-bill-wave"></i> {{ __('My payments') }}</a>
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
                    <i class="fas fa-comments"></i> {{ __('Messaging') }}
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