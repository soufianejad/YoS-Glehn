<ul class="list-unstyled components">
    <!-- General -->
    <li>
        <a href="#generalSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('author.dashboard*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('General') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('author.dashboard*') ? 'show' : '' }}" id="generalSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('author.dashboard*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('author.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> {{ __('Dashboard') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.favorites') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.favorites') }}">
                    <i class="fas fa-heart"></i> {{ __('My Favorites') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.profile') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.profile') }}">
                    <i class="fas fa-user"></i> {{ __('Profile') }}
                </a>
            </li>
        </ul>
    </li>

    <!-- My Books -->
    <li class="mt-1">
        <a href="#booksSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('author.books*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('My Books') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('author.books*') ? 'show' : '' }}" id="booksSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('author.books.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('author.books.index') }}">
                    <i class="fas fa-book"></i> {{ __('View All') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('author.books.create') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('author.books.create') }}">
                    <i class="fas fa-plus"></i> {{ __('Add New Book') }}
                </a>
            </li>
        </ul>
    </li>

    <!-- Statistics & Reviews -->
    <li class="mt-1">
        <a href="#statsReviewsSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('author.statistics*') || request()->routeIs('author.reviews*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Statistics & Reviews') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('author.statistics*') || request()->routeIs('author.reviews*') ? 'show' : '' }}" id="statsReviewsSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('author.statistics*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('author.statistics') }}">
                    <i class="fas fa-chart-line"></i> {{ __('Statistics') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('author.reviews*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('author.reviews') }}">
                    <i class="fas fa-star"></i> {{ __('Reviews') }}
                </a>
            </li>
        </ul>
    </li>

    <!-- Revenues -->
    <li class="mt-1">
        <a href="#revenuesSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('author.revenues*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Revenues') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('author.revenues*') ? 'show' : '' }}" id="revenuesSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('author.revenues.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('author.revenues.index') }}">
                    <i class="fas fa-chart-pie"></i> {{ __('Overview') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('author.revenues.details') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('author.revenues.details') }}">
                    <i class="fas fa-list"></i> {{ __('Details') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('author.revenues.history') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('author.revenues.history') }}">
                    <i class="fas fa-history"></i> {{ __('Payout History') }}
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('author.revenues.payout.request') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('author.revenues.payout.request') }}">
                    <i class="fas fa-hand-holding-usd"></i> {{ __('Request Payout') }}
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