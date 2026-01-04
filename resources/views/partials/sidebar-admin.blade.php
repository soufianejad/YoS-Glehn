<ul class="list-unstyled components">
    <!-- General -->
    <li>
        <a href="#generalSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('admin.dashboard*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none {{ request()->routeIs('admin.dashboard*') ? 'collapsed' : 'collapsed' }}">
            {{ __('General') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('admin.dashboard*') ? 'show' : '' }}" id="generalSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> {{ __('Dashboard') }}
                </a>
            </li>
        </ul>
    </li>
    <!-- Content Management -->
    <li class="mt-1">
        <a href="#contentSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('admin.books*') || request()->routeIs('admin.reviews*') || request()->routeIs('admin.categories*') || request()->routeIs('admin.tags*') || request()->routeIs('admin.pages*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Content Management') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('admin.books*') || request()->routeIs('admin.reviews*') || request()->routeIs('admin.categories*') || request()->routeIs('admin.tags*') || request()->routeIs('admin.pages*') ? 'show' : '' }}" id="contentSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.books*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.books.index') }}"><i class="fas fa-book"></i> {{ __('Books') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.reviews.index') }}"><i class="fas fa-star-half-alt"></i> {{ __('Reviews') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.categories.index') }}"><i class="fas fa-tags"></i> {{ __('Categories') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.tags*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.tags.index') }}"><i class="fas fa-hashtag"></i> {{ __('Tags') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.pages*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.pages.index') }}"><i class="fas fa-file-alt"></i> {{ __('Static Pages') }}</a>
            </li>
        </ul>
    </li>

    <!-- User Management -->
    <li class="mt-1">
        <a href="#userSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('admin.users*') || request()->routeIs('admin.schools*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('User Management') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('admin.users*') || request()->routeIs('admin.schools*') ? 'show' : '' }}" id="userSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.users.index') }}"><i class="fas fa-users"></i> {{ __('Users') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.schools*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.schools.index') }}"><i class="fas fa-school"></i> {{ __('Schools') }}</a>
            </li>
        </ul>
    </li>

    <!-- Monetization -->
    <li class="mt-1">
        <a href="#monetizationSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('admin.subscription-plans*') || request()->routeIs('admin.payments*') || request()->routeIs('admin.revenues*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Monetization') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('admin.subscription-plans*') || request()->routeIs('admin.payments*') || request()->routeIs('admin.revenues*') ? 'show' : '' }}" id="monetizationSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.subscription-plans*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.subscription-plans.index') }}"><i class="fas fa-id-card"></i> {{ __('Subscription Plans') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.payments.index') }}"><i class="fas fa-money-bill-wave"></i> {{ __('Payments') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.revenues*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.revenues.index') }}">
                    <i class="fas fa-chart-line"></i> {{ __('Revenues') }}
                    @if(isset($pendingRevenuesCount) && $pendingRevenuesCount > 0)
                        <span class="badge bg-warning float-end">{{ $pendingRevenuesCount }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </li>

    <!-- Tools & Engagement -->
    <li class="mt-1">
        <a href="#toolsSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('admin.quiz*') || request()->routeIs('admin.badges*') || request()->routeIs('admin.announcements*') || request()->routeIs('admin.messaging*') || request()->routeIs('admin.notifications*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Tools & Engagement') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('admin.quiz*') || request()->routeIs('admin.badges*') || request()->routeIs('admin.announcements*') || request()->routeIs('admin.messaging*') || request()->routeIs('admin.notifications*') ? 'show' : '' }}" id="toolsSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.quiz*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.quiz.index') }}"><i class="fas fa-question-circle"></i> {{ __('Quizzes') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.badges*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.badges.index') }}"><i class="fas fa-award"></i> {{ __('Badges') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.announcements*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.announcements.index') }}"><i class="fas fa-bullhorn"></i> {{ __('Announcements') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.messaging*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.messaging.index') }}">
                    <i class="fas fa-comments"></i> {{ __('Messaging') }}
                    @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                        <span class="badge bg-danger float-end">{{ $unreadMessagesCount }}</span>
                    @endif
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.notifications.index') }}"><i class="fas fa-history"></i> {{ __('Notification History') }}</a>
            </li>
        </ul>
    </li>

    <!-- System -->
    <li class="mt-1">
        <a href="#systemSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('admin.settings*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('System') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('admin.settings*') ? 'show' : '' }}" id="systemSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.settings.general') }}"><i class="fas fa-cog"></i> {{ __('Settings') }}</a>
            </li>
        </ul>
    </li>
</ul>
<style>
    .components .nav-link. {
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