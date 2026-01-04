@extends('layouts.dashboard')

@section('title', __('Gestion des Utilisateurs'))
@section('header', __('Gestion des Utilisateurs'))

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Liste des Utilisateurs') }} ({{ $users->total() }})</h6>
                </div>
                 <div class="col-md-8">
                    <form action="{{ route('admin.users.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('Rechercher par nom, email...') }}" value="{{ $search ?? '' }}">
                            <select name="role" class="form-select">
                                <option value="">{{ __('Tous les rôles') }}</option>
                                <option value="admin" {{ ($role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="author" {{ ($role ?? '') == 'author' ? 'selected' : '' }}>Auteur</option>
                                <option value="school" {{ ($role ?? '') == 'school' ? 'selected' : '' }}>École</option>
                                <option value="student" {{ ($role ?? '') == 'student' ? 'selected' : '' }}>Étudiant</option>
                                <option value="reader" {{ ($role ?? '') == 'reader' ? 'selected' : '' }}>Lecteur</option>
                                <option value="adult_reader" {{ ($role ?? '') == 'adult_reader' ? 'selected' : '' }}>Lecteur Adulte</option>
                            </select>
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                        </div>
                    </form>
                </div>
                <div class="col text-right">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-icon-split btn-sm">
                        <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                        <span class="text">{{ __('Ajouter') }}</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('Nom') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th class="text-center">{{ __('Rôle') }}</th>
                            <th class="text-center">{{ __('Statut') }}</th>
                            <th class="text-center">{{ __('Inscrit le') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle mr-3" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 font-weight-bold">{{ $user->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                                <td class="text-center">
                                    <span class="badge-primary">{{ ucfirst($user->role) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($user->is_active)
                                        <span class="badge-success">{{ __('Actif') }}</span>
                                    @else
                                        <span class="badge-danger">{{ __('Inactif') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info btn-circle btn-sm" title="{{__('Voir')}}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-circle btn-sm" title="{{__('Modifier')}}">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    @if(Auth::id() !== $user->id)
                                    <a href="{{ route('admin.users.impersonate', $user) }}" class="btn btn-secondary btn-circle btn-sm" title="{{__('Impersonate')}}">
                                        <i class="fas fa-user-secret"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-circle btn-sm" onclick="return confirm('{{ __('Êtes-vous sûr ?') }}')" title="{{__('Supprimer')}}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">{{ __('Aucun utilisateur trouvé.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
