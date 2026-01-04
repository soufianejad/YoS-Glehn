@extends('layouts.dashboard')

@section('title', 'Créer un Quiz - Choisir un Livre')
@section('header', 'Créer un Quiz')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Étape 1 sur 2 : Veuillez choisir un livre</h6>
        </div>
        <div class="card-body">
            <!-- Search Form -->
            <form action="{{ route('teacher.quizzes.select-book') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher un livre par titre..." value="{{ $search ?? '' }}">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                </div>
            </form>

            @if($books->isEmpty())
                <div class="text-center py-5">
                    <p class="text-muted">Aucun livre trouvé.</p>
                </div>
            @else
                <div class="row">
                    @foreach($books as $book)
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card h-100 text-center">
                                <img src="{{ $book->cover_image_url }}" class="card-img-top" alt="{{ $book->title }}" style="height: 200px; object-fit: cover;">
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title font-weight-bold">{{ $book->title }}</h6>
                                    <p class="card-text small text-muted flex-grow-1">par {{ $book->author->name ?? 'N/A' }}</p>
                                    <a href="{{ route('teacher.quizzes.create', $book) }}" class="btn btn-primary btn-sm mt-auto">
                                        Choisir ce livre <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center">
                    {{ $books->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
