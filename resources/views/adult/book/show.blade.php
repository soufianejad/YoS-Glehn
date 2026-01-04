@extends('layouts.dashboard')

@section('title', $book->title)
@section('header', 'Détails du Livre')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 col-lg-3 text-center">
                    <img src="{{ $book->cover_image_url }}" class="img-fluid rounded shadow" alt="{{ $book->title }}" style="max-height: 400px;">
                </div>
                <div class="col-md-8 col-lg-9">
                    <h1 class="h3 font-weight-bold">{{ $book->title }}</h1>
                    <p class="text-muted">par {{ $book->author->name ?? 'Auteur inconnu' }}</p>
                    
                    <div class="mb-4">
                        <p>{{ $book->description }}</p>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        @if($book->pdf_file)
                            @if($hasPurchasedPdf)
                                <a href="{{ route('adult.library.read', $book) }}" class="btn btn-lg btn-primary">
                                    <i class="fas fa-book-open me-2"></i> Lire (PDF)
                                </a>
                            @else
                                <form action="{{ route('adult.purchase.pdf', $book) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-lg btn-success">
                                        <i class="fas fa-shopping-cart me-2"></i> Acheter PDF ({{ number_format($book->pdf_price, 2) }} €)
                                    </button>
                                </form>
                            @endif
                        @endif

                        @if($book->audio_file)
                            @if($hasPurchasedAudio)
                                <a href="{{ route('adult.library.listen', $book) }}" class="btn btn-lg btn-info">
                                    <i class="fas fa-headphones me-2"></i> Écouter (Audio)
                                </a>
                            @else
                                <form action="{{ route('adult.purchase.audio', $book) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-lg btn-secondary">
                                        <i class="fas fa-shopping-cart me-2"></i> Acheter Audio ({{ number_format($book->audio_price, 2) }} €)
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                    
                    <hr class="my-4">
                    <a href="{{ route('adult.library.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour à la bibliothèque
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <h4 class="h5 mb-0">Avis des lecteurs</h4>
        </div>
        <div class="card-body">
            @auth
                <div class="mb-4">
                    <h5>Laisser un avis</h5>
                    {{-- Assuming a route 'adult.review.store' needs to be created --}}
                    <form action="#" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label for="rating" class="form-label">Note</label>
                            <select class="form-select" style="max-width: 150px;" id="rating" name="rating">
                                <option value="5">5 étoiles</option>
                                <option value="4">4 étoiles</option>
                                <option value="3">3 étoiles</option>
                                <option value="2">2 étoiles</option>
                                <option value="1">1 étoile</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="comment" class="form-label">Commentaire</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Envoyer l'avis</button>
                    </form>
                </div>
                <hr>
            @endauth

            @forelse($book->reviews()->latest()->get() as $review)
                <div class="d-flex mb-3 border-bottom pb-3">
                    <img src="{{ $review->user->avatar_url }}" class="rounded-circle" style="width: 50px; height: 50px;" alt="{{ $review->user->name }}">
                    <div class="ms-3">
                        <h6 class="mb-0">{{ $review->user->name }}</h6>
                        <div class="text-warning">
                            @for ($i = 0; $i < 5; $i++)
                                <i class="fas fa-star{{ $i < $review->rating ? '' : '-regular' }}"></i>
                            @endfor
                        </div>
                        <p class="mt-1 mb-0">{{ $review->comment }}</p>
                        <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @empty
                <p>Aucun avis pour le moment.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection