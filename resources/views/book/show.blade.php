{{-- resources/views/book/show.blade.php --}}
@extends('layouts.app')

@section('title', $book->title . ' - Livre')

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <div class="row g-5 align-items-start">
            <!-- Couverture du livre -->
            <div class="col-lg-4">
                <div class="text-center position-relative">
                    <img
                        src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default_book_cover.png') }}"
                        class="img-fluid rounded-3 shadow-lg transition-transform hover-scale"
                        alt="Couverture de {{ $book->title }}"
                        style="max-height: 520px; object-fit: cover;"
                        loading="lazy">

                    @auth
                        @if($readingProgress?->total_pages > 0)
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success fs-6 px-3 py-2 shadow">
                                    <i class="fas fa-book-reader me-1"></i>
                                    {{ round($readingProgress->progress_percentage) }}%
                                </span>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Détails principaux -->
            <div class="col-lg-8">
                <div class="mb-4">
                    <h1 class="display-5 fw-bold text-dark mb-2">{{ $book->title }}</h1>
                    <p class="fs-5 text-muted mb-1">
                        par
                        <a href=""
                           class="text-decoration-none text-primary fw-medium hover-underline">
                            {{ $book->author->name }}
                        </a>
                    </p>

                    <!-- Catégorie -->
                    @if($book->category)
                        <a href="{{ route('library.index', ['category' => $book->category->slug]) }}"
                           class="badge bg-primary text-white text-decoration-none fs-6 mb-3 d-inline-block px-3 py-2 hover-bg-dark">
                            {{ $book->category->name }}
                        </a>
                    @endif
                </div>

                <!-- Note moyenne -->
                <div class="d-flex align-items-center mb-3">
                    <div class="me-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $book->average_rating ? 'text-warning' : 'text-muted' }} fs-5"></i>
                        @endfor
                    </div>
                    <span class="text-muted small">
                        <strong>{{ number_format($book->average_rating, 1) }}</strong>
                        ({{ $book->reviews->where('is_approved', true)->count() }} {{ Str::plural('avis', $book->reviews->where('is_approved', true)->count()) }})
                    </span>
                </div>

                <!-- Description -->
                <div class="bg-white p-4 rounded-3 shadow-sm mb-4 lh-lg fs-5 text-dark">
                    {!! nl2br(e($book->description)) !!}
                </div>

                <!-- Progression (si connecté et en cours) -->
                @auth
                    @if($readingProgress && $readingProgress->total_pages > 0)
                        <div class="mb-4">
                            <p class="mb-2 fw-semibold text-success">
                                <i class="fas fa-book-reader me-2"></i> {{ __('Votre progression de lecture') }}
                            </p>
                            <div class="progress" style="height: 24px;">
                                <div class="progress-bar bg-success"
                                     role="progressbar"
                                     style="width: {{ $readingProgress->progress_percentage }}%"
                                     aria-valuenow="{{ $readingProgress->progress_percentage }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                    <span class="fw-bold">{{ round($readingProgress->progress_percentage) }}%</span>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1">
                                Page {{ $readingProgress->current_page }} / {{ $readingProgress->total_pages }}
                            </small>
                        </div>
                    @endif

                    @if($audioProgress && $audioProgress->total_duration > 0)
                        <div class="mb-4">
                            <p class="mb-2 fw-semibold text-info">
                                <i class="fas fa-headphones me-2"></i> {{ __("Votre progression d'écoute") }}
                            </p>
                            <div class="progress" style="height: 24px;">
                                <div class="progress-bar bg-info"
                                     role="progressbar"
                                     style="width: {{ $audioProgress->progress_percentage }}%"
                                     aria-valuenow="{{ $audioProgress->progress_percentage }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                    <span class="fw-bold">{{ round($audioProgress->progress_percentage) }}%</span>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1">
                                {{ gmdate("H:i:s", $audioProgress->current_position) }} / {{ gmdate("H:i:s", $audioProgress->total_duration) }}
                            </small>
                        </div>
                    @endif
                @endauth

                <!-- Actions principales -->
                <div class="bg-white p-4 rounded-3 shadow-sm">
                    @auth
                        <!-- Favoris -->
                        <form action="{{ route('favorites.toggle', $book) }}" method="POST" class="mb-4 favorite-form">
                            @csrf
                            <button type="submit"
                                    class="btn {{ auth()->user()->favorites->contains($book->id) ? 'btn-danger' : 'btn-outline-danger' }} w-100 btn-lg hover-shadow"
                                    id="favorite-button">
                                <i class="{{ auth()->user()->favorites->contains($book->id) ? 'fas' : 'far' }} fa-heart me-2"></i>
                                <span id="favorite-button-text">{{ auth()->user()->favorites->contains($book->id) ? 'Retirer des favoris' : 'Ajouter aux favoris' }}</span>
                            </button>
                        </form>

                        <!-- PDF Access -->
                        @if($book->pdf_file)
                            <div class="mb-4">
                                <h5 class="mb-3 text-primary">
                                    <i class="fas fa-file-pdf me-2"></i> {{ __('Accès au PDF') }}
                                </h5>

                                @if($hasPurchasedBook)
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('read.book', $book) }}"
                                           class="btn btn-primary btn-lg hover-shadow">
                                            <i class="fas fa-book-open me-2"></i> {{ __('Lire maintenant') }}
                                        </a>
                                        @if($book->is_downloadable)
                                            <a href="{{ route('book.secure_download', $book) }}"
                                               class="btn btn-outline-info btn-lg hover-shadow">
                                                <i class="fas fa-download me-2"></i> {{ __('Télécharger le PDF') }}
                                            </a>
                                        @endif
                                    </div>

                                @elseif($hasActiveSubscription)
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('read.book', $book) }}"
                                           class="btn btn-success btn-lg hover-shadow">
                                            <i class="fas fa-book-open me-2"></i> {{ __('Lire avec votre abonnement') }}
                                        </a>
                                    </div>

                                    @if($book->is_downloadable)
                                        <div class="border rounded-3 p-3 text-center mt-3 bg-light">
                                            <p class="mb-2 fw-medium">{{ __('Téléchargement réduit pour abonnés') }}</p>
                                            <p class="fs-5 mb-3">
                                                <del class="text-muted me-2">{{ number_format($book->pdf_price, 0) }} XOF</del>
                                                <span class="fw-bold text-success fs-4">{{ number_format($finalPdfPrice, 0) }} XOF</span>
                                            </p>
                                            <form action="{{ route('purchase.pdf', $book) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-info w-100 hover-shadow">
                                                    <i class="fas fa-download me-2"></i> Télécharger ({{ number_format($finalPdfPrice, 0) }} XOF)
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                @else
                                    <div class="border rounded-3 p-4 text-center bg-light">
                                        <h5 class="mb-2">{{ __('Acheter le PDF') }}</h5>
                                        <p class="fs-4 fw-bold text-primary mb-3">{{ number_format($finalPdfPrice, 0) }} XOF</p>
                                        <form action="{{ route('purchase.pdf', $book) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary w-100 btn-lg hover-shadow">
                                                <i class="fas fa-shopping-cart me-2"></i> {{ __('Acheter maintenant') }}
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Audio Access -->
                        @if($book->audio_file)
                            <div class="mb-4 pt-3 border-top">
                                <h5 class="mb-3 text-success">
                                    <i class="fas fa-headphones me-2"></i> {{ __('Écoute audio') }}
                                </h5>
                                @if(auth()->user()->hasAccessToBook($book))
                                    <a href="{{ route('listen.book', $book) }}"
                                       class="btn btn-success btn-lg w-100 hover-shadow">
                                        <i class="fas fa-play me-2"></i> {{ __('Écouter maintenant') }}
                                    </a>
                                @else
                                    <div class="text-center p-3 border rounded-3 bg-light">
                                        <p class="fs-5 fw-bold text-success mb-3">{{ number_format($book->audio_price, 0) }} XOF</p>
                                        <form action="{{ route('purchase.audio', $book) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success w-100 hover-shadow">
                                                <i class="fas fa-shopping-cart me-2"></i> {{ __("Acheter l'audio") }}
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Quiz -->
                        @if($book->has_quiz && ($hasPurchasedBook || $hasActiveSubscription))
                            <div class="pt-3 border-top">
                                <a href="{{ route('quiz.start', $book->quizzes->first()) }}"
                                   class="btn btn-warning btn-lg w-100 text-dark hover-shadow">
                                    <i class="fas fa-question-circle me-2"></i> {{ __('Passer le quiz') }}
                                </a>
                            </div>
                        @endif
                    @endauth

                    @guest
                        <div class="alert alert-info text-center p-4 rounded-3 border-0 shadow-sm">
                            <i class="fas fa-info-circle fa-2x mb-3 text-info"></i>
                            <p class="mb-3 fs-5">
                                <a href="{{ route('login') }}" class="alert-link fw-bold">{{ __('Connectez-vous') }}</a>
                                {{ __("pour accéder au livre, l'acheter ou le mettre en favoris.") }}
                            </p>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary">
                                {{ __('Créer un compte') }}
                            </a>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section Avis + Détails -->
<div class="container my-5">
    <div class="row g-5">
        <!-- Avis -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h3 class="mb-4 d-flex align-items-center">
                        <i class="fas fa-comments me-2 text-primary"></i>
                        Avis des lecteurs
                        <span class="badge bg-primary ms-2">{{ $book->reviews->where('is_approved', true)->count() }}</span>
                    </h3>

                    @forelse($book->reviews->where('is_approved', true) as $review)
                        <div class="d-flex gap-3 mb-4 pb-4 border-bottom last-border-0">
                            <img src="{{ $review->user->avatar ? asset('storage/' . $review->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($review->user->name) . '&background=0D8ABC&color=fff' }}"
                                 alt="Avatar de {{ $review->user->name }}"
                                 class="rounded-circle flex-shrink-0"
                                 width="56" height="56" loading="lazy">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 fw-semibold">{{ $review->user->name }}</h6>
                                    <small class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
                                </div>
                                <div class="text-warning small mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                                <p class="mb-0 text-dark lh-base">{{ $review->comment }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">
                            <i class="fas fa-comment-slash fa-2x mb-3 text-muted"></i><br>
                            {{ __('Aucun avis pour le moment. Soyez le premier à partager votre expérience !') }}
                        </p>
                    @endforelse

                    <!-- Formulaire d'avis -->
                    @auth
                        @if(auth()->user()->hasAccessToBook($book))
                            <hr class="my-5">
                            <h4 class="mb-4">
                                <i class="fas fa-pen me-2 text-primary"></i> {{ __('Laisser un avis') }}
                            </h4>
                            <form action="{{ route('review.store', $book) }}" method="POST" id="review-form">
                                @csrf
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Votre note <span class="text-danger">*</span></label>
                                    <div class="star-rating d-flex gap-1" role="radiogroup" aria-label="Note sur 5 étoiles">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" name="rating" value="{{ $i }}" id="star{{ $i }}"
                                                   class="btn-check" required>
                                            <label for="star{{ $i }}" class="btn btn-outline-warning px-3 py-2"
                                                   data-bs-toggle="tooltip" title="{{ $i }} étoile{{ $i > 1 ? 's' : '' }}">
                                                <i class="far fa-star"></i>
                                            </label>
                                        @endfor
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="comment" class="form-label fw-semibold">Votre commentaire <span class="text-danger">*</span></label>
                                    <textarea name="comment" id="comment" rows="4" class="form-control"
                                              placeholder="{{ __('Partagez votre expérience de lecture...') }}" required></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg px-4 hover-shadow">
                                    <i class="fas fa-paper-plane me-2"></i> {{ __('Publier mon avis') }}
                                </button>
                            </form>
                        @else
                            <div class="alert alert-light border rounded-3 p-4 text-center">
                                <i class="fas fa-lock fa-2x mb-3 text-warning"></i>
                                <p class="mb-0">
                                    Vous devez <strong>{{ __('acheter ou accéder au livre') }}</strong> {{ __('pour laisser un avis.') }}
                                </p>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        <!-- Détails du livre -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i> {{ __('Informations') }}
                    </h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between py-3">
                        <span class="fw-semibold text-muted">{{ __('Année') }}</span>
                        <span>{{ $book->published_year ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between py-3">
                        <span class="fw-semibold text-muted">{{ __('Langue') }}</span>
                        <span>{{ $book->language ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between py-3">
                        <span class="fw-semibold text-muted">{{ __('ISBN') }}</span>
                        <span>{{ $book->isbn ?? '—' }}</span>
                    </li>
                    @if($book->pdf_file)
                        <li class="list-group-item d-flex justify-content-between py-3">
                            <span class="fw-semibold text-muted">{{ __('Pages') }}</span>
                            <span><strong>{{ $book->pdf_pages }}</strong> {{ __('pages') }}</span>
                        </li>
                    @endif
                    @if($book->audio_file)
                        <li class="list-group-item d-flex justify-content-between py-3">
                            <span class="fw-semibold text-muted">{{ __('Durée audio') }}</span>
                            <span><strong>{{ $book->audio_duration }}</strong> {{ __('min') }}</span>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Tags -->
            @if($book->tags->count())
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-tags me-2"></i> {{ __('Tags') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($book->tags as $tag)
                            <a href="{{ route('library.index', ['tag' => $tag->slug]) }}"
                               class="badge bg-secondary text-decoration-none me-1 mb-1 px-3 py-2 hover-bg-dark">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Related Books Section -->
<div class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-5">{{ __('Vous pourriez aussi aimer') }}</h2>
        <div class="row">
            @forelse($relatedBooks as $relatedBook)
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card book-card h-100 shadow-sm border-0">
                         <a href="{{ route('book.show', $relatedBook->slug) }}">
                            <img src="{{ $relatedBook->cover_image_url }}" class="card-img-top" alt="{{ $relatedBook->title }}">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><a href="{{ route('book.show', $relatedBook->slug) }}" class="text-dark">{{ Str::limit($relatedBook->title, 50) }}</a></h5>
                            <p class="card-text text-muted mb-2">{{ $relatedBook->author->name }}</p>
                            <div class="star-rating mb-3">
                                @php $rating = $relatedBook->reviews->avg('rating'); @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $rating ? 'text-warning' : 'text-gray-300' }}"></i>
                                @endfor
                                <span class="text-muted small">({{ $relatedBook->reviews->count() }})</span>
                            </div>
                            <a href="{{ route('book.show', $relatedBook->slug) }}" class="btn btn-primary mt-auto">{{ __('Voir Détails') }}</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted">{{__('Aucun livre similaire trouvé.')}}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .hover-shadow { transition: all 0.3s ease; }
    .hover-shadow:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important; transform: translateY(-2px); }

    .hover-underline { position: relative; }
    .hover-underline::after {
        content: ''; position: absolute; bottom: -2px; left: 0; width: 0;
        height: 2px; background: currentColor; transition: width 0.3s ease;
    }
    .hover-underline:hover::after { width: 100%; }

    .hover-scale { transition: transform 0.4s ease; }
    .hover-scale:hover { transform: scale(1.03); }

    .hover-bg-dark:hover { background-color: #0d6efd !important; }

    .last-border-0:last-child { border-bottom: 0 !important; }

    .star-rating label {
        font-size: 1.5rem;
        transition: all 0.2s ease;
    }
    .star-rating label:hover,
    .star-rating input:checked ~ label {
        color: #ffc107 !important;
    }
    .star-rating input:checked ~ label i {
        font-weight: 900;
    }
</style>
@endpush

@push('scripts')
<script>
    // Étoiles interactives
    document.querySelectorAll('.star-rating input').forEach(input => {
        input.addEventListener('change', () => {
            const value = input.value;
            const labels = input.closest('.star-rating').querySelectorAll('label');
            labels.forEach((label, idx) => {
                const starIndex = 5 - idx;
                const icon = label.querySelector('i');
                if (starIndex <= value) {
                    label.classList.add('text-warning');
                    icon.classList.replace('far', 'fas');
                } else {
                    label.classList.remove('text-warning');
                    icon.classList.replace('fas', 'far');
                }
            });
        });
    });

    // Tooltips
    document.addEventListener('DOMContentLoaded', () => {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
    });
</script>
@endpush