<ul class="list-unstyled components">
    <!-- General -->
    <li class="sidebar-heading">{{ __("General") }}</li>
    <li class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.dashboard') }}"><i class="fas fa-tachometer-alt"></i> {{ __("Dashboard") }}</a>
    </li>

    <!-- My School -->
    <li class="sidebar-heading mt-4">{{ __("My School") }}</li>
    <li class="nav-item {{ request()->routeIs('student.school.info') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.school.info') }}"><i class="fas fa-info-circle"></i> {{ __("School Info") }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('student.school.classes') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.school.classes') }}"><i class="fas fa-chalkboard-teacher"></i> {{ __("My Classes") }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('student.school.announcements') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.school.announcements') }}"><i class="fas fa-bullhorn"></i> {{ __("Announcements") }}</a>
    </li>

    <!-- Library -->
    <li class="sidebar-heading mt-4">{{ __("Library") }}</li>
    <li class="nav-item {{ request()->routeIs('student.library.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.library.index') }}"><i class="fas fa-book-open"></i> {{ __("Browse Library") }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('student.library.assigned') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.library.assigned') }}"><i class="fas fa-tasks"></i> {{ __("Assigned Books") }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('reader.favorites') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('reader.favorites') }}"><i class="fas fa-heart"></i> {{ __("My Favorites") }}</a>
    </li>

    <!-- My Progress -->
    <li class="sidebar-heading mt-4">{{ __("My Progress") }}</li>
    <li class="nav-item {{ request()->routeIs('student.progress.reading') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.progress.reading') }}"><i class="fas fa-book-reader"></i> {{ __("Reading Progress") }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('reader.quizzes') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('reader.quizzes') }}"><i class="fas fa-question-circle"></i> {{ __("Quiz Results") }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('reader.badges') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('reader.badges') }}"><i class="fas fa-award"></i> {{ __("My Badges") }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('student.progress.leaderboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('student.progress.leaderboard') }}"><i class="fas fa-trophy"></i> {{ __("Leaderboard") }}</a>
    </li>

    <!-- Account -->
    <li class="sidebar-heading mt-4">{{ __("Account") }}</li>
    <li class="nav-item {{ request()->routeIs('reader.profile') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('reader.profile') }}"><i class="fas fa-user-cog"></i> {{ __("My Profile") }}</a>
    </li>
    <li class="nav-item {{ request()->routeIs('messaging.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('messaging.index') }}"><i class="fas fa-comments"></i> {{ __("Messaging") }}
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
