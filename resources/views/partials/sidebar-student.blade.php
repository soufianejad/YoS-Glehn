<ul class="list-unstyled components">
    <!-- General -->
    <li class="sidebar-heading">{{ __('Général') }}</li>
    <li class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.dashboard') }}"><i class="fas fa-tachometer-alt"></i> {{ __('Tableau de Bord') }}</a>
    </li>

    <!-- My School -->
    <li class="sidebar-heading mt-4">{{ __('Mon École') }}</li>
    <li class="nav-item {{ request()->routeIs('student.school.info') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.school.info') }}"><i class="fas fa-info-circle"></i> {{ __('Infos École') }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('student.school.classes') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.school.classes') }}"><i class="fas fa-chalkboard-teacher"></i> {{ __('Mes Classes') }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('student.school.classmates') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.school.classmates') }}"><i class="fas fa-users"></i> {{ __('Camarades') }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('student.school.announcements') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.school.announcements') }}"><i class="fas fa-bullhorn"></i> {{ __('Annonces') }}</a>
    </li>

    <!-- Library -->
    <li class="sidebar-heading mt-4">{{ __('Bibliothèque') }}</li>
    <li class="nav-item {{ request()->routeIs('student.library.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.library.index') }}"><i class="fas fa-book-open"></i> {{ __('Explorer') }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('student.library.recommended') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.library.recommended') }}"><i class="fas fa-star"></i> {{ __('Recommandés') }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('student.library.assigned') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.library.assigned') }}"><i class="fas fa-tasks"></i> {{ __('Livres Assignés') }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('student.library.search') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.library.search') }}"><i class="fas fa-search"></i> {{ __('Recherche') }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('reader.favorites') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('reader.favorites') }}"><i class="fas fa-heart"></i> {{ __('Mes Favoris') }}</a>
    </li>

    <!-- My Progress -->
    <li class="sidebar-heading mt-4">{{ __('Mes Progrès') }}</li>
    <li class="nav-item {{ request()->routeIs('student.progress.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.progress.index') }}"><i class="fas fa-chart-line"></i> {{ __('Aperçu') }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('student.progress.reading') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.progress.reading') }}"><i class="fas fa-book-reader"></i> {{ __('Progression Lecture') }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('student.progress.listening') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.progress.listening') }}"><i class="fas fa-headphones"></i> {{ __('Progression Audio') }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('student.quiz.index') || request()->routeIs('student.progress.quizzes') || request()->routeIs('reader.quizzes') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.quiz.index') }}"><i class="fas fa-question-circle"></i> {{ __('Résultats Quiz') }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('student.progress.badges') || request()->routeIs('reader.badges') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.progress.badges') }}"><i class="fas fa-award"></i> {{ __('Mes Badges') }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('student.progress.certificates.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.progress.certificates.index') }}"><i class="fas fa-certificate"></i> {{ __('Mes Certificats') }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('student.progress.leaderboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.progress.leaderboard') }}"><i class="fas fa-trophy"></i> {{ __('Classement') }}</a>
    </li>

    <!-- Account -->
    <li class="sidebar-heading mt-4">{{ __('Compte') }}</li>
    <li class="nav-item {{ request()->routeIs('student.profile') || request()->routeIs('reader.profile') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.profile') }}"><i class="fas fa-user-cog"></i> {{ __('Mon Profil') }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('messaging.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('messaging.index') }}"><i class="fas fa-comments"></i> {{ __('Messagerie') }}
            {{-- You can add an unread messages count here if available --}}
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
