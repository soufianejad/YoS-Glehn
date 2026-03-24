@extends('layouts.dashboard')

@section('title', __('Gestion des Utilisateurs'))
@section('header', __('Gestion des Utilisateurs'))

@push('styles')
<style>
    .user-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
    .user-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .user-avatar {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .role-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
    }
    .status-active { background-color: #28a745; }
    .status-inactive { background-color: #dc3545; }
    
    .card-actions {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .user-card:hover .card-actions {
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Search and Filter Header -->
    <div class="card shadow-sm mb-4 border-0 rounded-lg">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-lg-3">
                    <h5 class="mb-0 font-weight-bold text-primary">
                        <i class="fas fa-users me-2"></i>{{ __('Utilisateurs') }} 
                        <span class="badge bg-light text-primary ms-2">{{ $users->total() }}</span>
                    </h5>
                </div>
                <div class="col-lg-7 my-3 my-lg-0">
                    <form action="{{ route('admin.users.index') }}" method="GET">
                        <div class="input-group bg-light rounded-pill p-1">
                            <input type="text" name="search" class="form-control border-0 bg-transparent ps-3" placeholder="{{ __('Rechercher par nom, email...') }}" value="{{ $search ?? '' }}">
                            <select name="role" class="form-select border-0 bg-transparent" style="max-width: 180px;">
                                <option value="">{{ __('Tous les rôles') }}</option>
                                <option value="admin" {{ ($role ?? '') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                                <option value="author" {{ ($role ?? '') == 'author' ? 'selected' : '' }}>{{ __('Auteur') }}</option>
                                <option value="school" {{ ($role ?? '') == 'school' ? 'selected' : '' }}>{{ __('École') }}</option>
                                <option value="student" {{ ($role ?? '') == 'student' ? 'selected' : '' }}>{{ __('Étudiant') }}</option>
                                <option value="reader" {{ ($role ?? '') == 'reader' ? 'selected' : '' }}>{{ __('Lecteur') }}</option>
                                <option value="adult_reader" {{ ($role ?? '') == 'adult_reader' ? 'selected' : '' }}>{{ __('Lecteur Adulte') }}</option>
                            </select>
                            <button class="btn btn-primary rounded-pill px-4" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-lg-2 text-lg-end">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="fas fa-plus me-2"></i>{{ __('Ajouter') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Grid -->
    <div class="row">
        @forelse($users as $user)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card user-card shadow-sm h-100 position-relative">
                    <div class="card-body text-center p-4">
                        <!-- Top Actions (Visible on Hover) -->
                        <div class="position-absolute top-0 end-0 p-2 card-actions">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-lg">
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.users.show', $user) }}"><i class="fas fa-eye me-2 text-info"></i>{{ __('Voir Profil') }}</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.users.edit', $user) }}"><i class="fas fa-pencil-alt me-2 text-warning"></i>{{ __('Modifier') }}</a></li>
                                    @if(Auth::id() !== $user->id)
                                        <li><a class="dropdown-item py-2" href="{{ route('admin.users.impersonate', $user) }}"><i class="fas fa-user-secret me-2 text-secondary"></i>{{ __('Incarner') }}</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('{{ __('Êtes-vous sûr ?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item py-2 text-danger"><i class="fas fa-trash me-2"></i>{{ __('Supprimer') }}</button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <!-- Avatar -->
                        <div class="mb-3">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="user-avatar rounded-circle">
                        </div>

                        <!-- Info -->
                        <h6 class="mb-1 font-weight-bold">{{ $user->name }}</h6>
                        <p class="text-muted small mb-3">
                            <a href="mailto:{{ $user->email }}" class="text-decoration-none text-muted">
                                <i class="far fa-envelope me-1"></i>{{ Str::limit($user->email, 25) }}
                            </a>
                        </p>

                        <!-- Role Badge -->
                        <div class="mb-3">
                            @php
                                $roleClass = [
                                    'admin' => 'bg-danger text-white',
                                    'author' => 'bg-primary text-white',
                                    'school' => 'bg-success text-white',
                                    'student' => 'bg-info text-white',
                                    'reader' => 'bg-secondary text-white',
                                    'adult_reader' => 'bg-dark text-white'
                                ][$user->role] ?? 'bg-light text-dark';
                            @endphp
                            <span class="role-badge {{ $roleClass }}">
                                {{ str_replace('_', ' ', ucfirst($user->role)) }}
                            </span>
                        </div>

                        <!-- Footer Info -->
                        <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                            <div class="small text-muted">
                                <i class="far fa-calendar-alt me-1"></i>{{ $user->created_at->format('M Y') }}
                            </div>
                            <div class="small">
                                <span class="status-indicator {{ $user->is_active ? 'status-active' : 'status-inactive' }}"></span>
                                <span class="{{ $user->is_active ? 'text-success' : 'text-danger' }} font-weight-bold">
                                    {{ $user->is_active ? __('Actif') : __('Inactif') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-users-slash fa-4x text-light"></i>
                </div>
                <h5 class="text-muted">{{ __('Aucun utilisateur trouvé.') }}</h5>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $users->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
