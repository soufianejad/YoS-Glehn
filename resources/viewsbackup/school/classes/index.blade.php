@extends('layouts.dashboard')

@section('title', __('Gestion des Classes'))
@section('header', __('Gestion des Classes'))

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Liste des Classes') }} ({{ $classes->total() }})</h6>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('school.classes.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('Rechercher par nom, niveau...') }}" value="{{ $search ?? '' }}">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                        </div>
                    </form>
                </div>
                <div class="col text-right">
                    <a href="{{ route('school.classes.create') }}" class="btn btn-primary btn-icon-split btn-sm">
                        <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                        <span class="text">{{ __('Créer une Classe') }}</span>
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
                            <th>{{ __('Niveau') }}</th>
                            <th>{{ __('Enseignant') }}</th>
                            <th class="text-center">{{ __('Étudiants') }}</th>
                            <th class="text-center">{{ __('Statut') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $class)
                            <tr>
                                <td>
                                    <h6 class="mb-0 font-weight-bold">{{ $class->name }}</h6>
                                </td>
                                <td>{{ $class->level }}</td>
                                <td>{{ $class->teacher->name ?? 'Non assigné' }}</td>
                                <td class="text-center">{{ $class->students_count }}</td>
                                <td class="text-center">
                                    @if($class->is_active)
                                        <span class="badge badge-success">{{ __('Actif') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('Inactif') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('school.classes.show', $class) }}" class="btn btn-info btn-circle btn-sm" title="{{__('Voir')}}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('school.classes.edit', $class) }}" class="btn btn-warning btn-circle btn-sm" title="{{__('Modifier')}}">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('school.classes.destroy', $class) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-circle btn-sm" onclick="return confirm('{{ __('Êtes-vous sûr ?') }}')" title="{{__('Supprimer')}}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">{{ __('Aucune classe trouvée.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $classes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
