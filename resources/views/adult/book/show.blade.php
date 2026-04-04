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
                                    {{ __('Lire (PDF)') }}
                                </a>
                            @else
                                <form action="{{ route('adult.purchase.pdf', $book) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-lg btn-success">
                                        <i class="fas fa-shopping-cart me-2"></i> Acheter PDF ({{ formatPrice($book->pdf_price) }})
                                    </button>
                                </form>
                            @endif
                        @endif

                        @if($book->audio_file)
                            @if($hasPurchasedAudio)
                                <a href="{{ route('adult.library.listen', $book) }}" class="btn btn-lg btn-info">
                                    {{ __('Écouter (Audio)') }}
                                </a>
                            @else
                                <form action="{{ route('adult.purchase.audio', $book) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-lg btn-secondary">
                                        <i class="fas fa-shopping-cart me-2"></i> Acheter Audio ({{ formatPrice($book->audio_price) }})
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                    
                    <hr class="my-4">
                    <a href="{{ route('adult.library.index') }}" class="btn btn-outline-secondary">
                        {{ __('Retour à la bibliothèque') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white">
            <h4 class="h5 mb-0"><i class="fas fa-comments me-2 text-primary"></i> {{ __('Avis des lecteurs') }} <span class="badge bg-primary ms-1">{{ $book->approvedReviews->count() }}</span></h4>
        </div>
        <div class="card-body">
            @auth
                @php
                    $userPendingReview = $book->reviews()->where('user_id', auth()->id())->where('is_approved', false)->first();
                @endphp

                @if($userPendingReview)
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i> {{ __('Votre avis est en cours de validation par l\'équipe.') }}
                    </div>
                @elseif($book->approvedReviews()->where('user_id', auth()->id())->exists())
                     <div class="alert alert-light mb-4 border">
                        <i class="fas fa-check-circle me-2 text-success"></i> {{ __('Vous avez déjà laissé un avis pour ce livre.') }}
                    </div>
                @else
                    <div class="mb-4">
                        <h5 class="mb-3">{{ __('Laisser un avis') }}</h5>
                        <form action="{{ route('adult.library.review.store', $book) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="rating" class="form-label">{{ __('Note') }}</label>
                                <select class="form-select" style="max-width: 150px;" id="rating" name="rating" required>
                                    <option value="5">5 {{ __('étoiles') }}</option>
                                    <option value="4">4 {{ __('étoiles') }}</option>
                                    <option value="3">3 {{ __('étoiles') }}</option>
                                    <option value="2">2 {{ __('étoiles') }}</option>
                                    <option value="1">1 {{ __('étoile') }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">{{ __('Commentaire') }}</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="{{ __('Partagez votre avis sur ce livre...') }}" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i> {{ __("Envoyer l'avis") }}
                            </button>
                        </form>
                    </div>
                @endif
                <hr class="my-4">
            @endauth

            @forelse($book->approvedReviews()->latest()->get() as $review)
                <div class="d-flex mb-4 last-border-0">
                    <img src="{{ $review->user->avatar_url }}" class="rounded-circle shadow-sm" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $review->user->name }}">
                    <div class="ms-3 flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="mb-0 fw-bold">{{ $review->user->name }}</h6>
                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="text-warning small mb-2">
                            @for ($i = 0; $i < 5; $i++)
                                <i class="fas fa-star{{ $i < $review->rating ? '' : '-regular' }}"></i>
                            @endfor
                        </div>
                        <p class="text-dark mb-0">{{ $review->comment }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="far fa-comment-dots fa-3x text-muted mb-3"></i>
                    <p class="text-muted">{{ __('Aucun avis approuvé pour le moment.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection