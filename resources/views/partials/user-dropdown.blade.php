<div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
    {{-- User Info Header --}}
    <div class="px-4 py-3">
        <div class="d-flex align-items-center">
            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
            <div>
                <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                <small class="text-muted">{{ Auth::user()->email }}</small>
            </div>
        </div>
    </div>
    <div class="dropdown-divider"></div>

    {{-- Universal Links --}}
    <a class="dropdown-item" href="{{ route('profile') }}">
        <i class="fas fa-user fa-fw me-2"></i> {{ __('My Profile') }}
    </a>
    <a class="dropdown-item" href="{{ route('messaging.index') }}">
        <i class="fas fa-envelope fa-fw me-2"></i> {{ __('Messaging') }}
    </a>

    {{-- Role-Specific Management Links --}}
    @if(Auth::user()->isAdmin())
        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-user-shield fa-fw me-2"></i> {{ __('Admin Portal') }}
        </a>
    @elseif(Auth::user()->isAuthor())
        <a class="dropdown-item" href="{{ route('author.dashboard') }}">
            <i class="fas fa-pen-alt fa-fw me-2"></i> {{ __('Author Portal') }}
        </a>
    @elseif(Auth::user()->isSchool())
        <a class="dropdown-item" href="{{ route('school.dashboard') }}">
            <i class="fas fa-school fa-fw me-2"></i> {{ __('School Portal') }}
        </a>
    @elseif(Auth::user()->isTeacher())
        <a class="dropdown-item" href="{{ route('teacher.dashboard') }}">
            <i class="fas fa-chalkboard-teacher fa-fw me-2"></i> {{ __('Teacher Portal') }}
        </a>
    @elseif(Auth::user()->isParent())
        <a class="dropdown-item" href="{{ route('parent.dashboard') }}">
            <i class="fas fa-user-friends fa-fw me-2"></i> {{ __('Parent Portal') }}
        </a>
    @endif

    {{-- Role-Specific Action Links --}}
    <div class="dropdown-divider"></div>

    @if(Auth::user()->isStudent())
        <a class="dropdown-item" href="{{ route('student.library.index') }}">
            <i class="fas fa-book-reader fa-fw me-2"></i> {{ __('My Library') }}
        </a>
        <a class="dropdown-item" href="{{ route('student.school.classes') }}">
            <i class="fas fa-chalkboard fa-fw me-2"></i> {{ __('My Classes') }}
        </a>
        <a class="dropdown-item" href="{{ route('student.progress.index') }}">
            <i class="fas fa-chart-line fa-fw me-2"></i> {{ __('My Progress') }}
        </a>
    @elseif(Auth::user()->isReader() || Auth::user()->isAdultReader())
        <a class="dropdown-item" href="{{ Auth::user()->isAdultReader() ? route('adult.library.index') : route('reader.library') }}">
            <i class="fas fa-book-reader fa-fw me-2"></i> {{ __('My Library') }}
        </a>
        <a class="dropdown-item" href="{{ route('subscription.index') }}">
            <i class="fas fa-id-card fa-fw me-2"></i> {{ __('My Subscription') }}
        </a>
        <a class="dropdown-item" href="{{ route('reader.favorites') }}">
            <i class="fas fa-heart fa-fw me-2"></i> {{ __('My Favorites') }}
        </a>
    @elseif(Auth::user()->isAuthor())
        <a class="dropdown-item" href="{{ route('author.books.index') }}">
            <i class="fas fa-book fa-fw me-2"></i> {{ __('My Books') }}
        </a>
        <a class="dropdown-item" href="{{ route('author.revenues.index') }}">
            <i class="fas fa-euro-sign fa-fw me-2"></i> {{ __('My Revenues') }}
        </a>
    @elseif(Auth::user()->isTeacher())
         <a class="dropdown-item" href="{{ route('teacher.dashboard') }}">
            <i class="fas fa-chalkboard-teacher fa-fw me-2"></i> {{ __('My Classes') }}
        </a>
    @elseif(Auth::user()->isParent())
        <a class="dropdown-item" href="{{ route('parent.dashboard') }}">
            <i class="fas fa-users fa-fw me-2"></i> {{ __('My Children') }}
        </a>
    @endif

    <div class="dropdown-divider"></div>

    {{-- Logout --}}
    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt fa-fw me-2"></i> {{ __('Logout') }}
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</div>
