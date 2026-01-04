@extends('layouts.dashboard')

@section('title', __('Gestion des Étudiants'))
@section('header', __('Gestion des Étudiants'))

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Liste des Étudiants') }} ({{ $students->total() }})</h6>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('school.students.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('Rechercher par nom, email...') }}" value="{{ $search ?? '' }}">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                        </div>
                    </form>
                </div>
                <div class="col text-right">
                    <a href="{{ route('school.students.create') }}" class="btn btn-primary btn-icon-split btn-sm">
                        <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                        <span class="text">{{ __('Ajouter') }}</span>
                    </a>
                     <a href="{{ route('school.students.import.create') }}" class="btn btn-success btn-icon-split btn-sm">
                        <span class="icon text-white-50"><i class="fas fa-file-import"></i></span>
                        <span class="text">{{ __('Importer') }}</span>
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
                            <th class="text-center">{{ __('Classes') }}</th>
                            <th class="text-center">{{ __('Statut') }}</th>
                            <th class="text-center">{{ __('Date d\'inscription') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $student->avatar_url }}" alt="{{ $student->name }}" class="rounded-circle mr-3" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 font-weight-bold">{{ $student->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td><a href="mailto:{{ $student->email }}">{{ $student->email }}</a></td>
                                <td class="text-center">{{ $student->classes()->count() }}</td>
                                <td class="text-center">
                                    @if($student->is_active)
                                        <span class="badge badge-success">{{ __('Actif') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('Inactif') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $student->created_at->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('school.students.show', $student) }}" class="btn btn-info btn-circle btn-sm" title="{{__('Voir')}}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('school.students.edit', $student) }}" class="btn btn-warning btn-circle btn-sm" title="{{__('Modifier')}}">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('school.students.destroy', $student) }}" method="POST" class="d-inline">
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
                                <td colspan="6" class="text-center py-4">{{ __('Aucun étudiant trouvé.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
