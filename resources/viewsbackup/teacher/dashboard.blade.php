@extends('layouts.dashboard')

@section('title', 'Tableau de bord Professeur')
@section('header', 'Tableau de bord')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Bienvenue, {{ $teacher->name }} !</h1>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-start-primary shadow-sm">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Mes Classes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $classes->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 border-start-success shadow-sm">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total des Élèves</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $studentsCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">

        <!-- Classes List -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Aperçu des Classes</h6>
                    <a href="{{ route('teacher.dashboard') }}" class="btn btn-sm btn-outline-primary">Toutes les classes</a>
                </div>
                <div class="card-body">
                    @if($classes->isEmpty())
                        <p class="text-gray-500">Vous n'êtes assigné à aucune classe pour le moment.</p>
                        <a href="#">Contacter l'administrateur de l'école</a>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($classes as $class)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 font-weight-bold">{{ $class->name }}</p>
                                        <small class="text-muted">{{ $class->students_count }} {{ Str::plural('élève', $class->students_count) }}</small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('teacher.assignments.create', $class) }}" class="btn btn-sm btn-primary" title="Assigner un livre">
                                            <i class="fas fa-book"></i>
                                        </a>
                                        <a href="{{ route('teacher.progress.index', $class) }}" class="btn btn-sm btn-info" title="Voir la progression">
                                            <i class="fas fa-chart-line"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Announcements -->
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dernières Annonces de l'école</h6>
                </div>
                <div class="card-body">
                    @if($announcements->isEmpty())
                        <p class="text-center text-muted mt-3">Aucune annonce récente.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($announcements as $announcement)
                                <li class="list-group-item">
                                    <p class="font-weight-bold mb-1">{{ $announcement->title }}</p>
                                    <p class="small text-muted mb-1">{{ $announcement->content }}</p>
                                    <small class="text-muted text-xs">{{ $announcement->created_at->format('d/m/Y') }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- School Library Section -->
    <div class="row mt-2">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bibliothèque de l'Établissement</h6>
                </div>
                <div class="card-body">
                    @if($schoolBooks->isEmpty())
                        <p class="text-center text-muted">Aucun livre dans l'espace éducatif pour le moment.</p>
                    @else
                        <div class="row">
                            @foreach($schoolBooks as $book)
                                <div class="col-lg-6 mb-4">
                                    <div class="card h-100 border-0 shadow-hover">
                                        <div class="row g-0">
                                            <div class="col-md-4" style="flex: 0 0 120px;">
                                                <img src="{{ $book->cover_image_url }}" class="img-fluid rounded-start" alt="{{ $book->title }}" style="width: 100%; height: 180px; object-fit: cover;">
                                            </div>
                                            <div class="col-md-8" style="flex: 1;">
                                                <div class="card-body d-flex flex-column h-100">
                                                    <h5 class="card-title">{{ Str::limit($book->title, 50) }}</h5>
                                                    <p class="card-text small text-muted">par {{ $book->author->name ?? 'Auteur inconnu' }}</p>
                                                    <p class="card-text small">{{ Str::limit($book->description, 80) }}</p>
                                                    {{-- In a future step, a modal could be triggered here to select a class to assign to --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $schoolBooks->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection