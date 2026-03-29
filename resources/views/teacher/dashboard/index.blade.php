@extends('layouts.teacher')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2>{{ __('Tableau de Bord Enseignant') }} - {{ $school->name }}</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100 bg-primary text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">{{ __('Classes Assignées') }}</h5>
                    <p class="card-text display-4">{{ $classes->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h5 class="mb-0">{{ __('Vos Classes') }}</h5>
        </div>
        <div class="card-body">
            @if($classes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Nom') }}</th>
                                <th>{{ __('Niveau') }}</th>
                                <th>{{ __('Étudiants') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classes as $class)
                                <tr>
                                    <td>{{ $class->name }}</td>
                                    <td>{{ $class->level }}</td>
                                    <td>{{ $class->students_count }}</td>
                                    <td>
                                        <a href="{{ route('teacher.classes.show', $class) }}" class="btn btn-sm btn-info text-white">{{ __('Voir détails') }}</a>
                                        <a href="{{ route('teacher.progress.index', $class) }}" class="btn btn-sm btn-success">{{ __('Suivi Progrès') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">{{ __('Vous n\'avez aucune classe assignée pour le moment.') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
