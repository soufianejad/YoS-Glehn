<ul class="list-unstyled components">
    <!-- General -->
    <li>
        <a href="#generalSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('teacher.dashboard*') || request()->routeIs('teacher.classes.index') || request()->routeIs('teacher.progress.*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Général') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('teacher.dashboard*') || request()->routeIs('teacher.classes.index') || request()->routeIs('teacher.progress.*') ? 'show' : '' }}" id="generalSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('teacher.dashboard*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('teacher.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> {{ __('Dashboard') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('teacher.classes.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('teacher.classes.index') }}">
                    <i class="fas fa-school"></i> {{ __('My Classes') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('teacher.progress.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('teacher.progress.list-classes') }}">
                    <i class="fas fa-chart-line"></i> {{ __('Student Progress') }}
                </a>
            </li>
        </ul>
    </li>

    <!-- Quiz Management -->
    <li class="mt-1">
        <a href="#quizSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('teacher.quizzes*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __("Quiz Management") }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('teacher.quizzes*') ? 'show' : '' }}" id="quizSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('teacher.quizzes.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('teacher.quizzes.index') }}">
                    <i class="fas fa-list"></i> {{ __('All Quizzes') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('teacher.quizzes.select-book') || request()->routeIs('teacher.quizzes.create') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('teacher.quizzes.select-book') }}">
                    <i class="fas fa-plus-circle"></i> {{ __('Create Quiz') }}
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
            {{ __("My account") }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('reader.profile') ? 'show' : '' }}" id="accountSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.profile') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.profile') }}"><i class="fas fa-user-cog"></i> {{ __("My Profile") }}</a>
            </li>
        </ul>
    </li>
    
    <!-- Communication -->
    <li class="mt-1">
        <a href="#communicationSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('messaging.index') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __("Communication") }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('messaging.index') ? 'show' : '' }}" id="communicationSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('messaging.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('messaging.index') }}">
                    <i class="fas fa-comments"></i> {{ __("Messaging") }}
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
