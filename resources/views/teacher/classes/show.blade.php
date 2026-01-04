@extends('layouts.dashboard')

@section('title', 'Gestion de la Classe')
@section('header', $class->name)

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Élèves</h6>
            <div class="btn-group" role="group">
                <a href="{{ route('teacher.progress.index', $class) }}" class="btn btn-outline-success">
                    <i class="fas fa-chart-line me-1"></i> Voir la Progression
                </a>
                <a href="{{ route('teacher.assignments.create', $class) }}" class="btn btn-outline-primary">
                    <i class="fas fa-book-medical me-1"></i> Assigner un livre
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($class->students->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Il n'y a aucun élève dans cette classe pour le moment.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($class->students as $student)
                                <tr>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->email }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        <div class="card-footer">
            <a href="{{ route('teacher.classes.index') }}" class="btn btn-light btn-sm">
                &larr; Retour à toutes les classes
            </a>
        </div>
    </div>
</div>
@endsection