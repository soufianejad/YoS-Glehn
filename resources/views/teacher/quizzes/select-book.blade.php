@extends('layouts.teacher')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2>{{ __('Sélectionner un livre pour créer un Quiz') }}</h2>
        </div>
    </div>

    <div class="row">
        @forelse($books as $book)
            <div class="col-md-3 mb-4">
                <div class="card shadow-sm h-100 border-0">
                    @if($book->cover_image)
                        <img src="{{ Storage::url($book->cover_image) }}" class="card-img-top" alt="{{ $book->title }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-book fa-3x"></i>
                        </div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $book->title }}</h5>
                        <p class="card-text text-muted small flex-grow-1">{{ Str::limit($book->description, 100) }}</p>
                        <a href="{{ route('teacher.quizzes.create', $book) }}" class="btn btn-primary w-100 mt-auto">{{ __('Créer un Quiz') }}</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted">{{ __('Aucun livre éducatif disponible pour le moment.') }}</p>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $books->links() }}
    </div>
</div>
@endsection
