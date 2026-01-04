<ul class="list-unstyled components">
    <li class="nav-item {{ request()->routeIs('school.dashboard*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('school.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> {{ __('Dashboard') }}
        </a>
    </li>

    <li class="nav-item">
        <a href="#managementSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('school.students*') || request()->routeIs('school.teachers*') || request()->routeIs('school.parents*') || request()->routeIs('school.classes*') ? 'true' : 'false' }}" class="dropdown-toggle nav-link">
            <i class="fas fa-users-cog"></i> {{ __('Management') }}
        </a>
        <ul class="collapse list-unstyled {{ request()->routeIs('school.students*') || request()->routeIs('school.teachers*') || request()->routeIs('school.parents*') || request()->routeIs('school.classes*') ? 'show' : '' }}" id="managementSubmenu">
            <li class="nav-item {{ request()->routeIs('school.students*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.students.index') }}">
                    <i class="fas fa-user-graduate"></i> {{ __('Students') }}
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('school.teachers*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.teachers.index') }}">
                    <i class="fas fa-chalkboard-teacher"></i> {{ __('Teachers') }}
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('school.parents*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.parents.index') }}">
                    <i class="fas fa-user-shield"></i> {{ __('Parents') }}
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('school.classes*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.classes.index') }}">
                    <i class="fas fa-school"></i> {{ __('Classes') }}
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item">
        <a href="#contentSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('school.books.assignments*') || request()->routeIs('school.announcements*') ? 'true' : 'false' }}" class="dropdown-toggle nav-link">
            <i class="fas fa-book-open"></i> {{ __('Content') }}
        </a>
        <ul class="collapse list-unstyled {{ request()->routeIs('school.books.assignments*') || request()->routeIs('school.announcements*') ? 'show' : '' }}" id="contentSubmenu">
            <li class="nav-item {{ request()->routeIs('school.books.assignments*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.books.assignments.index') }}">
                    <i class="fas fa-book"></i> {{ __('Book Assignments') }}
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('school.announcements*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.announcements.index') }}">
                    <i class="fas fa-bullhorn"></i> {{ __('Announcements') }}
                </a>
            </li>
        </ul>
    </li>

    <li class="nav-item">
        <a href="#configSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('school.settings*') || request()->routeIs('school.qrcode*') || request()->routeIs('school.subscription*') ? 'true' : 'false' }}" class="dropdown-toggle nav-link">
            <i class="fas fa-cogs"></i> {{ __('Configuration') }}
        </a>
        <ul class="collapse list-unstyled {{ request()->routeIs('school.settings*') || request()->routeIs('school.qrcode*') || request()->routeIs('school.subscription*') ? 'show' : '' }}" id="configSubmenu">
            <li class="nav-item {{ request()->routeIs('school.settings*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.settings') }}">
                    <i class="fas fa-cog"></i> {{ __('Settings') }}
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('school.qrcode*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.qrcode') }}">
                    <i class="fas fa-qrcode"></i> {{ __('QR Code Sign-up') }}
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('school.subscription*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.subscription.index') }}">
                    <i class="fas fa-credit-card"></i> {{ __('Subscription') }}
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('school.profile*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('school.settings') }}"> {{-- Using settings as a placeholder for profile --}}
                    <i class="fas fa-user"></i> {{ __('Profile') }}
                </a>
            </li>
        </ul>
    </li>
    <li class="nav-item {{ request()->routeIs('messaging.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('messaging.index') }}"><i class="fas fa-comments"></i> {{ __('Messaging') }}
            @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                <span class="badge bg-danger float-right">{{ $unreadMessagesCount }}</span>
            @endif
        </a>
    </li>
</ul>