@extends('layouts.public')

@section('title', $book->title . ' - ' . config('platform.name'))

@section('meta_description', strip_tags(Str::limit($book->description, 150)))

@section('content')

<div class="container py-4">
    
    <!-- Book Details -->
    <div class="row">
        
        <!-- Left Column: Cover and Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                
                <!-- Cover Image -->
                <img src="{{ $book->cover_url }}" class="card-img-top" alt="{{ $book->title }}">
                
                <div class="card-body">
                    
                    <!-- Price Info -->
                    @auth
                        @if(auth()->user()->hasAccessToBook($book))
                            <div class="alert alert-success mb-3">
                                <i class="bi bi-check-circle"></i> {{ __('Vous avez accès à ce livre') }}
                            </div>
                        @else
                            <div class="mb-3">
                                <h5>{{ __('Prix') }}</h5>
                                @if($book->hasPdf())
                                    <p class="mb-1">
                                        <strong>{{ __('PDF :') }}</strong> {{ number_format($book->pdf_price, 0, ',', ' ') }} FCFA
                                    </p>
                                @endif
                                @if($book->hasAudio())
                                    <p class="mb-0">
                                        <strong>{{ __('Audio :') }}</strong> {{ number_format($book->audio_price, 0, ',', ' ') }} FCFA
                                    </p>
                                @endif
                            </div>
                        @endif
                    @endauth

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 mb-3">
                        @auth
                            @if(auth()->user()->hasAccessToBook($book))
                                @if($book->hasPdf())
                                    <a href="{{ route('read.book', $book->slug) }}" class="btn btn-primary">
                                        <i class="bi bi-book-half"></i> Lire (PDF)
                                    </a>
                                @endif
                                @if($book->hasAudio())
                                    <a href="{{ route('listen.book', $book->slug) }}" class="btn btn-success">
                                        <i class="bi bi-headphones"></i> Écouter (Audio)
                                    </a>
                                @endif
                                @if($book->hasPdf())
                                    <form action="{{ route('read.download', $book) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary w-100">
                                            <i class="bi bi-download"></i> {{ __('Télécharger') }}
                                        </button>
                                    </form>
                                @endif
                            @else
                                @if($book->hasPdf())
                                    <form action="{{ route('purchase.pdf', $book) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="bi bi-cart"></i> Acheter PDF ({{ number_format($book->pdf_price, 0, ',', ' ') }} FCFA)
                                        </button>
                                    </form>
                                @endif
                                @if($book->hasAudio())
                                    <form action="{{ route('purchase.audio', $book) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="bi bi-cart"></i> Acheter Audio ({{ number_format($book->audio_price, 0, ',', ' ') }} FCFA)
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('subscription.plans') }}" class="btn btn-warning w-100">
                                    <i class="bi bi-star"></i> {{ __("S'abonner pour accès illimité") }}
                                </a>
                            @endif

                            <!-- Favorite Button -->
                            @if($userProgress && $userProgress['is_favorite'])
                                <form action="{{ route('favorites.remove', $book) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-heart-fill"></i> {{ __('Retirer des favoris') }}
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('favorites.add', $book) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-heart"></i> {{ __('Ajouter aux favoris') }}
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> {{ __('Connectez-vous pour lire') }}
                            </a>
                        @endauth
                    </div>

                    <!-- Progress Bar (if user has started) -->
                    @auth
                        @if($userProgress)
                            @if($userProgress['reading'])
                                <div class="mb-3">
                                    <h6>{{ __('Progression de lecture') }}</h6>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $userProgress['reading']->progress_percentage }}%">
                                            {{ round($userProgress['reading']->progress_percentage) }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        Page {{ $userProgress['reading']->current_page }} / {{ $userProgress['reading']->total_pages }}
                                    </small>
                                </div>
                            @endif

                            @if($userProgress['audio'])
                                <div class="mb-3">
                                    <h6>{{ __("Progression d'écoute") }}</h6>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $userProgress['audio']->progress_percentage }}%">
                                            {{ round($userProgress['audio']->progress_percentage) }}%
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        {{ gmdate('H:i:s', $userProgress['audio']->current_position) }} / {{ $book->formatted_duration }}
                                    </small>
                                </div>
                            @endif
                        @endif
                    @endauth

                    <!-- Share Buttons -->
                    <hr>
                    <h6>{{ __('Partager') }}</h6>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-sm btn-outline-primary" title="{{ __('Facebook') }}">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-info" title="{{ __('Twitter') }}">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-success" title="{{ __('WhatsApp') }}">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Book Information -->
        <div class="col-lg-8">
            
            <!-- Title and Author -->
            <h1 class="mb-3">{{ $book->title }}</h1>
            <p class="lead text-muted mb-4">
                <i class="bi bi-person-circle"></i> {{ __('Par') }} <strong>{{ $book->author->full_name }}</strong>
            </p>

            <!-- Rating and Stats -->
            <div class="d-flex align-items-center mb-4 flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="text-warning fs-5 me-2">
                        @for($i = 0; $i < 5; $i++)
                            @if($i < floor($book->average_rating))
                                <i class="bi bi-star-fill"></i>
                            @elseif($i < ceil($book->average_rating))
                                <i class="bi bi-star-half"></i>
                            @else
                                <i class="bi bi-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="text-muted">{{ number_format($book->average_rating, 1) }} ({{ $book->reviews_count }} avis)</span>
                </div>
                
                <span class="text-muted">|</span>
                
                <span class="text-muted">
                    <i class="bi bi-eye"></i> {{ number_format($book->views_count) }} vues
                </span>
                
                <span class="text-muted">
                    <i class="bi bi-book"></i> {{ number_format($book->reads_count) }} lectures
                </span>

                @if($book->hasAudio())
                    <span class="text-muted">
                        <i class="bi bi-headphones"></i> {{ number_format($book->listens_count) }} écoutes
                    </span>
                @endif
            </div>

            <!-- Badges -->
            <div class="mb-4">
                <span class="badge bg-primary me-2">{{ $book->category->name }}</span>
                <span class="badge bg-secondary me-2">{{ config('platform.languages')[$book->language] ?? $book->language }}</span>
                @if($book->hasPdf())
                    <span class="badge bg-danger me-2"><i class="bi bi-file-pdf"></i> {{ __('PDF') }}</span>
                @endif
                @if($book->hasAudio())
                    <span class="badge bg-success me-2"><i class="bi bi-headphones"></i> Audio ({{ $book->formatted_duration }})</span>
                @endif
                @if($book->is_featured)
                    <span class="badge bg-warning"><i class="bi bi-star-fill"></i> {{ __('Coup de cœur') }}</span>
                @endif
            </div>

            <!-- Book Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        @if($book->isbn)
                            <div class="col-md-6 mb-2">
                                <strong>{{ __('ISBN :') }}</strong> {{ $book->isbn }}
                            </div>
                        @endif
                        @if($book->published_year)
                            <div class="col-md-6 mb-2">
                                <strong>{{ __('Année :') }}</strong> {{ $book->published_year }}
                            </div>
                        @endif
                        @if($book->pdf_pages)
                            <div class="col-md-6 mb-2">
                                <strong>{{ __('Pages :') }}</strong> {{ $book->pdf_pages }}
                            </div>
                        @endif
                        <div class="col-md-6 mb-2">
                            <strong>{{ __('Langue :') }}</strong> {{ config('platform.languages')[$book->language] ?? $book->language }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-3">{{ __('Description') }}</h4>
                    <div class="book-description">
                        {!! nl2br(e($book->description)) !!}
                    </div>
                </div>
            </div>

            <!-- Tags -->
            @if($book->tags->count() > 0)
                <div class="mb-4">
                    <h5>{{ __('Mots-clés') }}</h5>
                    @foreach($book->tags as $tag)
                        <span class="badge bg-light text-dark border me-2 mb-2">{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif

            <!-- Reviews Section -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h4 class="mb-0">Avis des lecteurs ({{ $book->reviews_count }})</h4>
                </div>
                <div class="card-body">
                    
                    <!-- Add Review Form -->
                    @auth
                        @if(!$userProgress || !$userProgress['has_reviewed'])
                            <div class="mb-4 p-3 bg-light rounded">
                                <h5>{{ __('Donnez votre avis') }}</h5>
                                <form action="{{ route('review.store', $book) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Note') }}</label>
                                        <div class="rating-input">
                                            @for($i = 5; $i >= 1; $i--)
                                                <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}" required>
                                                <label for="star{{ $i }}"><i class="bi bi-star-fill"></i></label>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Commentaire (optionnel)</label>
                                        <textarea name="comment" class="form-control" rows="3" placeholder="{{ __('Partagez votre avis sur ce livre...') }}"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send"></i> {{ __('Publier mon avis') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth

                    <!-- Reviews List -->
                    @forelse($book->reviews as $review)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-start">
                                <img src="{{ $review->user->avatar_url }}" alt="{{ $review->user->full_name }}" 
                                     class="rounded-circle me-3" width="50" height="50">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        {{ $review->user->full_name }}
                                        @if($review->is_verified_purchase)
                                            <span class="badge bg-success badge-sm">{{ __('Achat vérifié') }}</span>
                                        @endif
                                    </h6>
                                    <div class="text-warning mb-2">
                                        @for($i = 0; $i < 5; $i++)
                                            <i class="bi bi-star{{ $i < $review->rating ? '-fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                    @if($review->comment)
                                        <p class="mb-1">{{ $review->comment }}</p>
                                    @endif
                                    <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">{{ __('Aucun avis pour le moment. Soyez le premier à donner votre avis !') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Similar Books -->
    @if($similarBooks->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">{{ __('Livres similaires') }}</h3>
            </div>
            @foreach($similarBooks as $similar)
                <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
                    <a href="{{ route('book.show', $similar->slug) }}" class="text-decoration-none">
                        <div class="card h-100 shadow-sm">
                            <img src="{{ $similar->cover_url }}" class="card-img-top" alt="{{ $similar->title }}" 
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body p-2">
                                <h6 class="card-title small text-truncate" title="{{ $similar->title }}">
                                    {{ $similar->title }}
                                </h6>
                                <p class="text-muted small mb-0">{{ $similar->author->full_name }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

</div>

@endsection

@push('styles')
<style>
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    font-size: 2rem;
}

.rating-input input {
    display: none;
}

.rating-input label {
    color: #ddd;
    cursor: pointer;
    padding: 0 5px;
}

.rating-input input:checked ~ label,
.rating-input label:hover,
.rating-input label:hover ~ label {
    color: #ffc107;
}
</style>
@endpush