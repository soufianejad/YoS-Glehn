@extends('layouts.dashboard')

@section('title', 'Mes Favoris')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">
                <i class="fas fa-heart text-danger me-2"></i>Mes Livres Favoris
            </h2>
            <p class="text-muted mt-2">Retrouvez ici tous les livres que vous avez marqués comme favoris.</p>
        </div>
    </div>

    @if($favorites->count() > 0)
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            @foreach($favorites as $favorite)
                @php $book = $favorite->book; @endphp
                @if($book)
                <div class="col">
                    <div class="card h-100 book-card shadow-sm">
                        <div class="position-relative">
                            <img src="{{ Storage::url($book->cover_image) }}" class="card-img-top" alt="{{ $book->title }}" style="height: 250px; object-fit: cover;">

                            <form action="{{ route('books.favorite', $book) }}" method="POST" class="position-absolute top-0 end-0 m-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light btn-sm rounded-circle shadow-sm" title="Retirer des favoris">
                                    <i class="fas fa-heart text-danger"></i>
                                </button>
                            </form>

                            @if($book->is_premium)
                                <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">
                                    <i class="fas fa-star"></i> Premium
                                </span>
                            @endif
                        </div>

                        <div class="card-body">
                            <h5 class="card-title text-truncate" title="{{ $book->title }}">{{ $book->title }}</h5>
                            <p class="card-text small text-muted mb-2">
                                Par {{ $book->author_name ?? ($book->author ? $book->author->name : 'Auteur inconnu') }}
                            </p>

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <a href="{{ route('adult.library.show', $book) }}" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="fas fa-book-open me-1"></i> Lire
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $favorites->links() }}
        </div>
    @else
        <div class="text-center py-5 bg-white rounded shadow-sm">
            <div class="display-1 text-muted mb-4">
                <i class="far fa-heart"></i>
            </div>
            <h4>Aucun favori pour le moment</h4>
            <p class="text-muted">Explorez notre catalogue et ajoutez des livres à vos favoris pour les retrouver ici.</p>
            <a href="{{ route('adult.library.index') }}" class="btn btn-primary mt-3">
                <i class="fas fa-search me-2"></i>Explorer les livres
            </a>
        </div>
    @endif
</div>

<style>
.book-card {
    transition: transform 0.2s ease-in-out;
}
.book-card:hover {
    transform: translateY(-5px);
}
</style>
@endsection
