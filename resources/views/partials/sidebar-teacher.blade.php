<ul class="list-unstyled components">
    <li class="nav-item {{ request()->routeIs('teacher.dashboard*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('teacher.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> {{ __('Tableau de bord') }}
        </a>
    </li>
    <li class="nav-item {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('teacher.dashboard') }}">
            <i class="fas fa-school"></i> {{ __('Mes Classes') }}
        </a>
    </li>

    <li class="nav-item {{ request()->routeIs('teacher.progress.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('teacher.progress.list-classes') }}">
            <i class="fas fa-chart-line"></i> {{ __('Suivi des Élèves') }}
        </a>
    </li>

    <!-- Quiz Management -->
    <li class="sidebar-heading mt-4">{{ __("Gestion des Quiz") }}</li>
    <li class="nav-item {{ request()->routeIs('teacher.quizzes.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('teacher.quizzes.select-book') }}">
            <i class="fas fa-plus-circle"></i> {{ __('Créer un Quiz') }}
        </a>
    </li>

    <!-- My Account -->
    <li class="sidebar-heading mt-4">{{ __("Mon Compte") }}</li>
    <li class="nav-item {{ request()->routeIs('reader.profile') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('reader.profile') }}"><i class="fas fa-user-cog"></i> {{ __("Mon Profil") }}</a>
    </li>
    
    <!-- Communication -->
    <li class="sidebar-heading mt-4">{{ __("Communication") }}</li>
    <li class="nav-item {{ request()->routeIs('messaging.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('messaging.index') }}"><i class="fas fa-comments"></i> {{ __("Messagerie") }}
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
