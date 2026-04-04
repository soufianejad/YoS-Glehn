<ul class="list-unstyled components">
    <!-- General -->
    <li>
        <a href="#generalSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('admin.dashboard*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none {{ request()->routeIs('admin.dashboard*') ? 'collapsed' : 'collapsed' }}">
            {{ __('Général') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('admin.dashboard*') ? 'show' : '' }}" id="generalSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> {{ __('Tableau de Bord') }}
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
            {{ __('Gestion de Contenu') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('admin.books*') || request()->routeIs('admin.reviews*') || request()->routeIs('admin.categories*') || request()->routeIs('admin.tags*') || request()->routeIs('admin.pages*') ? 'show' : '' }}" id="contentSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.books*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.books.index') }}"><i class="fas fa-book"></i> {{ __('Livres') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.reviews.index') }}"><i class="fas fa-star-half-alt"></i> {{ __('Avis') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.categories.index') }}"><i class="fas fa-tags"></i> {{ __('Catégories') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.tags*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.tags.index') }}"><i class="fas fa-hashtag"></i> {{ __('Étiquettes') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.pages*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.pages.index') }}"><i class="fas fa-file-alt"></i> {{ __('Pages Statiques') }}</a>
            </li>
        </ul>
    </li>

    <!-- User Management -->
    <li class="mt-1">
        <a href="#userSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('admin.users*') || request()->routeIs('admin.schools*') || request()->routeIs('admin.adult-access*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Gestion des Utilisateurs') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('admin.users*') || request()->routeIs('admin.schools*') || request()->routeIs('admin.adult-access*') ? 'show' : '' }}" id="userSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.users.index') }}"><i class="fas fa-users"></i> {{ __('Utilisateurs') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.adult-access*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.users.adult-invitations') }}"><i class="fas fa-user-shield"></i> {{ __('Accès Adultes') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.schools*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.schools.index') }}"><i class="fas fa-school"></i> {{ __('Écoles') }}</a>
            </li>
        </ul>
    </li>

    <!-- Monetization -->
    <li class="mt-1">
        <a href="#monetizationSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('admin.subscription-plans*') || request()->routeIs('admin.payments*') || request()->routeIs('admin.revenues*') || request()->routeIs('admin.settings.payment') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Monétisation') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('admin.subscription-plans*') || request()->routeIs('admin.payments*') || request()->routeIs('admin.revenues*') || request()->routeIs('admin.settings.payment') ? 'show' : '' }}" id="monetizationSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.subscription-plans*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.subscription-plans.index') }}"><i class="fas fa-id-card"></i> {{ __("Plans d'Abonnement")}}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.payments.index') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.payments.index') }}"><i class="fas fa-money-bill-wave"></i> {{ __('Paiements') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.payments.monthly-report') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.payments.monthly-report') }}"><i class="fas fa-file-invoice-dollar"></i> {{ __('Rapport Mensuel') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.payments.annual-report') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.payments.annual-report') }}"><i class="fas fa-file-contract"></i> {{ __('Rapport Annuel') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.revenues.index') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.revenues.index') }}">
                    <i class="fas fa-chart-line"></i> {{ __('Revenus') }}
                    @if(isset($pendingRevenuesCount) && $pendingRevenuesCount > 0)
                        <span class="badge bg-warning float-end">{{ $pendingRevenuesCount }}</span>
                    @endif
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.revenues.authors') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.revenues.authors') }}"><i class="fas fa-user-tie"></i> {{ __('Auteurs & Revenus') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.revenues.payouts.index') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.revenues.payouts.index') }}"><i class="fas fa-hand-holding-usd"></i> {{ __('Paiements Auteurs') }}</a>
            </li>
        </ul>
    </li>

    <!-- Tools & Engagement -->
    <li class="mt-1">
        <a href="#toolsSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('admin.quiz*') || request()->routeIs('admin.badges*') || request()->routeIs('admin.announcements*') || request()->routeIs('admin.messaging*') || request()->routeIs('admin.notifications*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Outils & Engagement') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('admin.quiz*') || request()->routeIs('admin.badges*') || request()->routeIs('admin.announcements*') || request()->routeIs('admin.messaging*') || request()->routeIs('admin.notifications*') ? 'show' : '' }}" id="toolsSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.quiz*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.quiz.index') }}"><i class="fas fa-question-circle"></i> {{ __('Quiz') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.badges*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.badges.index') }}"><i class="fas fa-award"></i> {{ __('Badges') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.announcements*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.announcements.index') }}"><i class="fas fa-bullhorn"></i> {{ __('Annonces') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.messaging*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.messaging.index') }}">
                    <i class="fas fa-comments"></i> {{ __('Messagerie') }}
                    @if(isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                        <span class="badge bg-danger float-end">{{ $unreadMessagesCount }}</span>
                    @endif
                </a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.notifications*') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.notifications.index') }}"><i class="fas fa-history"></i> {{ __('Historique des Notifications') }}</a>
            </li>
        </ul>
    </li>

    <!-- Reports & Statistics -->
    <li class="mt-1">
        <a href="#reportsSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('admin.statistics*') || request()->routeIs('admin.activity.report') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Rapports & Stats') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('admin.statistics*') || request()->routeIs('admin.activity.report') ? 'show' : '' }}" id="reportsSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.statistics') }}"><i class="fas fa-chart-bar"></i> {{ __('Statistiques') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.activity.report') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.activity.report') }}"><i class="fas fa-clipboard-list"></i> {{ __("Rapport d'Activité") }}</a>
            </li>
        </ul>
    </li>

    <!-- System -->
    <li class="mt-1">
        <a href="#systemSubmenu"
           data-bs-toggle="collapse"
           aria-expanded="{{ request()->routeIs('admin.settings*') || request()->routeIs('admin.jobs*') ? 'true' : 'false' }}"
           class="dropdown-toggle nav-link sidebar-heading text-decoration-none">
            {{ __('Système') }}
        </a>
        <ul class="p-0 collapse {{ request()->routeIs('admin.settings*') || request()->routeIs('admin.jobs*') ? 'show' : '' }}" id="systemSubmenu">
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.settings.index') || request()->routeIs('admin.settings.general') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.settings.index') }}"><i class="fas fa-cog"></i> {{ __('Paramètres Généraux') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.settings.appearance') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.settings.appearance') }}"><i class="fas fa-paint-brush"></i> {{ __('Apparence') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.settings.email') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.settings.email') }}"><i class="fas fa-envelope"></i> {{ __('Emails') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.settings.payment') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.settings.payment') }}"><i class="fas fa-credit-card"></i> {{ __('Paramètres de Paiement') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.settings.languages') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.settings.languages') }}"><i class="fas fa-language"></i> {{ __('Langues') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.settings.currencies') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.settings.currencies') }}"><i class="fas fa-money-bill"></i> {{ __('Devises') }}</a>
            </li>
            <li style="display:block" class="nav-item {{ request()->routeIs('admin.jobs.index') ? 'active' : '' }}">
                <a class="nav-link " href="{{ route('admin.jobs.index') }}"><i class="fas fa-cogs"></i> {{ __('Tâches (Jobs)') }}</a>
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