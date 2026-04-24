@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h1 class="mb-4">{{ __('My Favorite Books') }}</h1>

    @if($favorites->isEmpty())
        <div class="alert alert-info" role="alert">
            {{ __("You haven't added any books to your favorites yet.") }}
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($favorites as $book)
                <div class="col book-card-col">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default_book_cover.png') }}" class="card-img-top" alt="{{ $book->title }}">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><a href="{{ route('book.show', $book->slug) }}" class="text-decoration-none text-dark">{{ $book->title }}</a></h5>
                            <p class="card-text text-muted">by {{ $book->author->name }}</p>


                            @php
                                $showPrices = \App\Models\Setting::where('key', 'platform.show_prices')->value('value') ?? '1';
                            @endphp
                            @if($showPrices == '1')
                            <!-- Prices -->
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @if($book->hasPdf())
                                <span class="badge bg-light text-dark border"><i class="bi bi-file-pdf text-danger"></i> {{ $book->pdf_price > 0 ? formatPrice($book->pdf_price) : __("Gratuit") }}</span>
                            @endif
                            @if($book->hasAudio())
                                <span class="badge bg-light text-dark border"><i class="bi bi-headphones text-primary"></i> {{ $book->audio_price > 0 ? formatPrice($book->audio_price) : __("Gratuit") }}</span>
                            @endif
                        </div>
                            @endif
                        <div class="mt-auto">
                                <form action="{{ route('favorites.toggle', $book) }}" method="POST" class="favorite-form">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                        <i class="fas fa-heart me-2"></i> {{ __('Remove from Favorites') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $favorites->links() }}
        </div>
    @endif
</div>
@endsection
