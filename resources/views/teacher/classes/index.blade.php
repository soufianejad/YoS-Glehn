@extends('layouts.dashboard')

@section('title', 'Mes Classes')
@section('header', 'Mes Classes')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            Liste de toutes les classes qui vous sont assignées
        </div>
        <div class="card-body">
            @if($classes->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-school fa-3x text-muted mb-3"></i>
                    <p class="text-muted">{{ __("Vous n'êtes assigné à aucune classe pour le moment.") }}</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">{{ __('Nom de la Classe') }}</th>
                                <th scope="col">{{ __("Nombre d'élèves") }}</th>
                                <th scope="col" class="text-end">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classes as $class)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $class->name }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $class->students_count }} {{ Str::plural('élève', $class->students_count) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('teacher.classes.show', $class) }}" class="btn btn-primary btn-sm">
                                            {{ __('Gérer la classe') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection