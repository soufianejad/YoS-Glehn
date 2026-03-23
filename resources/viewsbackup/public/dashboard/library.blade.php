@extends('layouts.dashboard')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">{{ __('Ma Bibliothèque') }}</h1>
            <p class="text-muted">{{ __('Retrouvez ici tous les livres que vous avez achetés individuellement.') }}</p>
            <hr>
        </div>
    </div>

    @if($purchases->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-book" style="font-size: 4rem;"></i>
            <h4 class="mt-3">{{ __('Votre bibliothèque est vide.') }}</h4>
            <p>{{ __('Les livres que vous achetez apparaîtront ici.') }}</p>
            <a href="{{ route('library.index') }}" class="btn btn-primary mt-3">{{ __('Explorer la bibliothèque publique') }}</a>
        </div>
    @else
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-4">
            @foreach($purchases as $purchase)
                @if($purchase->book)
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            @auth
                                <form action="{{ route('favorites.toggle', $purchase->book) }}" method="POST" class="position-absolute top-0 end-0 m-2 favorite-form" style="z-index: 10;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                        @if(auth()->user()->favorites->contains($purchase->book->id))
                                            <i class="fas fa-heart"></i>
                                        @else
                                            <i class="far fa-heart"></i>
                                        @endif
                                    </button>
                                </form>
                            @endauth
                            <a href="{{ route('book.show', $purchase->book->slug) }}" class="text-decoration-none">
                                <img src="{{ $purchase->book->cover_url ?? asset('images/default-cover.png') }}" class="card-img-top" alt="{{ $purchase->book->title }}" style="height: 200px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <h6 class="card-title small text-truncate" title="{{ $purchase->book->title }}">
                                        {{ $purchase->book->title }}
                                    </h6>
                                    @if($purchase->book->author)
                                        <p class="text-muted small mb-0">{{ $purchase->book->author->full_name }}</p>
                                    @endif
                                </div>
                            </a>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="mt-4">
            {{ $purchases->links() }}
        </div>
    @endif
</div>
@endsection
