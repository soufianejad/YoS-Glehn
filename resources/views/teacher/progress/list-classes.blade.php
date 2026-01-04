@extends('layouts.dashboard')

@section('title', 'Suivi des Élèves')
@section('header', 'Suivi des Élèves')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Veuillez sélectionner une classe pour voir la progression de ses élèves.</h6>
        </div>
        <div class="card-body">
            @if($classes->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-school fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Vous n'êtes assigné à aucune classe pour le moment.</p>
                </div>
            @else
                <div class="list-group">
                    @foreach($classes as $class)
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">{{ $class->name }}</h5>
                                <small class="text-muted">{{ $class->students_count }} {{ Str::plural('élève', $class->students_count) }}</small>
                            </div>
                            <a href="{{ route('teacher.progress.index', $class) }}" class="btn btn-primary">
                                <i class="fas fa-chart-line me-2"></i> Voir la Progression
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
