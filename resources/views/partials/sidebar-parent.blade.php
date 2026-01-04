<ul class="list-unstyled components">
    <li class="nav-item {{ request()->routeIs('parent.dashboard*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('parent.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> {{ __('Dashboard') }}
        </a>
    </li>
    <li class="nav-item {{ request()->routeIs('parent.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('parent.dashboard') }}">
            <i class="fas fa-child"></i> {{ __('My Children') }}
        </a>
    </li>

    <!-- My Account -->
    <li class="sidebar-heading mt-4">{{ __("My account") }}</li>
    <li class="nav-item {{ request()->routeIs('reader.profile') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('reader.profile') }}"><i class="fas fa-user-cog"></i> {{ __("My Profile") }}</a>
    </li>
    
    <!-- Communication -->
    <li class="sidebar-heading mt-4">{{ __("Communication") }}</li>
    <li class="nav-item {{ request()->routeIs('messaging.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('messaging.index') }}"><i class="fas fa-comments"></i> {{ __("Messaging") }}
            @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                <span class="badge bg-danger float-end">{{ $unreadMessagesCount }}</span>
            @endif
        </a>
    </li>
</ul>

<style>
    .sidebar-heading {
        padding: 10px 20px;
        font-size: 0.9em;
        text-transform: uppercase;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.4);
    }
</style>
