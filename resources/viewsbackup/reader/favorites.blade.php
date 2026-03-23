@extends('layouts.dashboard')

@section('title', __('Mes Favoris'))
@section('header', __('Mes Livres Favoris'))

@push('styles')
<style>
    .book-card .card-img-top {
        height: 300px;
        object-fit: cover;
    }
    .star-rating {
        color: #ffc107;
    }
    .empty-state {
        text-align: center;
        padding: 4rem;
        border: 2px dashed #e3e6f0;
        border-radius: 0.75rem;
    }
    .empty-state i {
        font-size: 4rem;
        color: #e3e6f0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    @if($favorites->isEmpty())
        <div class="empty-state bg-white shadow-sm">
            <i class="fas fa-heart-crack mb-4"></i>
            <h3 class="text-gray-800">{{ __("Votre liste de favoris est vide") }}</h3>
            <p class="text-muted">{{ __("Parcourez la bibliothèque pour trouver des livres qui vous plaisent et ajoutez-les à vos favoris !") }}</p>
            <a href="{{ route('library.index') }}" class="btn btn-primary mt-3">
                <i class="fas fa-book-open mr-2"></i> {{ __('Explorer la Bibliothèque') }}
            </a>
        </div>
    @else
        <div class="row">
            @foreach($favorites as $book)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card book-card h-100 shadow-sm border-0">
                        <a href="{{ route('book.show', $book->slug) }}">
                            <img src="{{ $book->cover_image_url }}" class="card-img-top" alt="{{ $book->title }}">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><a href="{{ route('book.show', $book->slug) }}" class="text-dark">{{ Str::limit($book->title, 50) }}</a></h5>
                            <p class="card-text text-muted mb-2">{{ $book->author->name }}</p>
                            <div class="star-rating mb-3">
                                @php $rating = $book->average_rating; @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $rating ? 'text-warning' : 'text-gray-300' }}"></i>
                                @endfor
                                <span class="text-muted small">({{ $book->reviews->count() }})</span>
                            </div>
                            <div class="mt-auto">
                                <a href="{{ route('read.book', $book) }}" class="btn btn-primary btn-sm w-100 mb-2">
                                    <i class="fas fa-book-open me-1"></i> 
                                    {{ ($book->readingProgress->first() && $book->readingProgress->first()->current_page > 0) ? __('Continuer') : __('Commencer') }}
                                </a>
                                <form action="{{ route('favorites.toggle', $book) }}" method="POST" class="favorite-form">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                        <i class="fas fa-trash me-1"></i> {{__('Retirer')}}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $favorites->links() }}
        </div>
    @endif
</div>
@endsection

