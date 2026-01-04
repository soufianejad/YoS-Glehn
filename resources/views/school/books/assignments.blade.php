@extends('layouts.dashboard')

@section('title', __('Gestion des Assignations'))
@section('header', __('Gestion des Assignations de Livres'))

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Liste des Assignations') }} ({{ $assignments->total() }})</h6>
                </div>
                 <div class="col-md-6">
                    <form action="{{ route('school.books.assignments.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('Rechercher par livre ou par classe...') }}" value="{{ $search ?? '' }}">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                        </div>
                    </form>
                </div>
                <div class="col text-right">
                    <a href="{{ route('school.books.assignments.create') }}" class="btn btn-primary btn-icon-split btn-sm">
                        <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                        <span class="text">{{ __('Assigner un Livre') }}</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('Livre') }}</th>
                            <th>{{ __('Classe') }}</th>
                            <th class="text-center">{{ __('Date d\'assignation') }}</th>
                            <th class="text-center">{{ __('Date Limite') }}</th>
                            <th class="text-center">{{ __('Obligatoire') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                            <tr>
                                <td>
                                    <h6 class="mb-0 font-weight-bold">{{ $assignment->book->title ?? __('N/A') }}</h6>
                                    <small class="text-muted">{{ $assignment->book->author->name ?? ''}}</small>
                                </td>
                                <td>{{ $assignment->class->name ?? __('N/A') }}</td>
                                <td class="text-center">{{ $assignment->assigned_at->format('d/m/Y') }}</td>
                                <td class="text-center">{{ $assignment->due_date ? $assignment->due_date->format('d/m/Y') : 'N/A' }}</td>
                                <td class="text-center">
                                    @if($assignment->is_mandatory)
                                        <span class="badge badge-danger">{{ __('Oui') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('Non') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{-- <a href="{{ route('school.books.assignments.edit', $assignment) }}" class="btn btn-warning btn-circle btn-sm" title="{{__('Modifier')}}">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a> --}}
                                    <form action="{{ route('school.books.assignments.destroy', $assignment) }}" method="POST" class="d-inline">
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
                                <td colspan="6" class="text-center py-4">{{ __('Aucune assignation trouvée.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="d-flex justify-content-center mt-3">
                {{ $assignments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
