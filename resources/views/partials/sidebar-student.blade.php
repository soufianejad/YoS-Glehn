<ul class="list-unstyled components">
    <!-- General -->
    <li>
        <a href="#generalSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('student.dashboard') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Général') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('student.dashboard') ? 'show' : '' }}" id="generalSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.dashboard') }}"><i class="fas fa-tachometer-alt"></i> {{ __('Tableau de Bord') }}</a>
            </li>
        </ul>
    </li>

    <!-- My School -->
    <li class="mt-1">
        <a href="#schoolSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('student.school.*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Mon École') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('student.school.*') ? 'show' : '' }}" id="schoolSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('student.school.info') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.school.info') }}"><i class="fas fa-info-circle"></i> {{ __('Infos École') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('student.school.classes') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.school.classes') }}"><i class="fas fa-chalkboard-teacher"></i> {{ __('Mes Classes') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('student.school.classmates') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.school.classmates') }}"><i class="fas fa-users"></i> {{ __('Camarades') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('student.school.announcements') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.school.announcements') }}"><i class="fas fa-bullhorn"></i> {{ __('Annonces') }}</a>
            </li>
        </ul>
    </li>

    <!-- Library -->
    <li class="mt-1">
        <a href="#librarySubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('student.library.*') || request()->routeIs('reader.favorites') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Bibliothèque') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('student.library.*') || request()->routeIs('reader.favorites') ? 'show' : '' }}" id="librarySubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('student.library.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.library.index') }}"><i class="fas fa-book-open"></i> {{ __('Explorer') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('student.library.recommended') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.library.recommended') }}"><i class="fas fa-star"></i> {{ __('Recommandés') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('student.library.assigned') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.library.assigned') }}"><i class="fas fa-tasks"></i> {{ __('Livres Assignés') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('student.library.search') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.library.search') }}"><i class="fas fa-search"></i> {{ __('Recherche') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('reader.favorites') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('reader.favorites') }}"><i class="fas fa-heart"></i> {{ __('Mes Favoris') }}</a>
            </li>
        </ul>
    </li>

    <!-- My Progress -->
    <li class="mt-1">
        <a href="#progressSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('student.progress.*') || request()->routeIs('student.quiz.index') || request()->routeIs('reader.quizzes') || request()->routeIs('reader.badges') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Mes Progrès') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('student.progress.*') || request()->routeIs('student.quiz.index') || request()->routeIs('reader.quizzes') || request()->routeIs('reader.badges') ? 'show' : '' }}" id="progressSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('student.progress.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.progress.index') }}"><i class="fas fa-chart-line"></i> {{ __('Aperçu') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('student.progress.reading') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.progress.reading') }}"><i class="fas fa-book-reader"></i> {{ __('Progression Lecture') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('student.progress.listening') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.progress.listening') }}"><i class="fas fa-headphones"></i> {{ __('Progression Audio') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('student.quiz.index') || request()->routeIs('student.progress.quizzes') || request()->routeIs('reader.quizzes') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.quiz.index') }}"><i class="fas fa-question-circle"></i> {{ __('Résultats Quiz') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('student.progress.badges') || request()->routeIs('reader.badges') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.progress.badges') }}"><i class="fas fa-award"></i> {{ __('Mes Badges') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('student.progress.certificates.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.progress.certificates.index') }}"><i class="fas fa-certificate"></i> {{ __('Mes Certificats') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('student.progress.leaderboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.progress.leaderboard') }}"><i class="fas fa-trophy"></i> {{ __('Classement') }}</a>
            </li>
        </ul>
    </li>

    <!-- Account -->
    <li class="mt-1">
        <a href="#accountSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('student.profile') || request()->routeIs('reader.profile') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Compte') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('student.profile') || request()->routeIs('reader.profile') ? 'show' : '' }}" id="accountSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('student.profile') || request()->routeIs('reader.profile') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('student.profile') }}"><i class="fas fa-user-cog"></i> {{ __('Mon Profil') }}</a>
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
