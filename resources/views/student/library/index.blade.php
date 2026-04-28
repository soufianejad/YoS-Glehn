@extends('layouts.dashboard')

@section('title', isset($category) ? $category->name : __('Bibliothèque Éducative'))
@section('header', isset($category) ? $category->name : __('Bibliothèque Éducative'))

@push('styles')
<style>
    .book-card .card-img-top {
        height: 250px;
        object-fit: cover;
    }
    .star-rating {
        color: #ffc107;
    }
    .filter-card {
        border-radius: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Filter and Search Form -->
    <div class="card shadow-sm mb-4 filter-card">
        <div class="card-body">
            <form action="{{ route('student.library.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label font-weight-bold">{{ __('Rechercher') }}</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('Titre, auteur...') }}">
                </div>
                <div class="col-md-4">
                    <label for="category" class="form-label font-weight-bold">{{ __('Catégorie') }}</label>
                    <select name="category" id="category" class="form-select">
                        <option value="">{{ __('Toutes') }}</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->slug }}" @if(isset($category) && $cat->id === $category->id) selected @endif>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">{{ __('Filtrer') }}</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-gray-800">
            @if(isset($category))
                {{ $category->name }}
            @elseif($search)
                {{ __('Résultats pour') }} "{{ $search }}"
            @else
                {{ __('Tous les livres') }}
            @endif
        </h3>
        <span class="text-muted">{{ $books->total() }} {{ __('livres trouvés') }}</span>
    </div>

    <div class="row">
        @forelse($books as $book)
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card book-card h-100 shadow-sm border-0">
                    <a href="{{ route('student.book.show', $book->slug) }}">
                        <img src="{{ $book->cover_image_url }}" class="card-img-top" alt="{{ $book->title }}">
                    </a>
                    <div class="card-body d-flex flex-column p-3">
                        <h6 class="card-title font-weight-bold mb-1"><a href="{{ route('student.book.show', $book->slug) }}" class="text-gray-900">{{ Str::limit($book->title, 40) }}</a></h6>
                        @if($book->author)
                            <p class="small mb-2"><a href="{{ route('public.author.show', $book->author) }}" class="text-decoration-none text-muted">{{ $book->author->name }}</a></p>
                        @endif



                            @php
                                $showPrices = \App\Models\Setting::where('key', 'platform.show_prices')->value('value') ?? '1';
                            @endphp
                            @if($showPrices == '1')
                            <!-- Prices -->
                            <div class="d-flex flex-column gap-2 mb-3 mt-auto" style="position: relative; z-index: 2;">
                                @if($book->hasPdf())
                                    <div class="d-flex align-items-center justify-content-between px-2 py-1 rounded bg-light border">
                                        <small class="text-muted"><i class="bi bi-file-pdf text-danger me-1"></i> {{ __('Ebook') }}</small>
                                        <span class="fw-bold" style="font-size: 0.9rem;">{{ $book->pdf_price > 0 ? formatPrice($book->pdf_price) : __("Gratuit") }}</span>
                                    </div>
                                @endif
                                @if($book->hasAudio())
                                    <div class="d-flex align-items-center justify-content-between px-2 py-1 rounded bg-light border">
                                        <small class="text-muted"><i class="bi bi-headphones text-primary me-1"></i> {{ __('Audio') }}</small>
                                        <span class="fw-bold" style="font-size: 0.9rem;">{{ $book->audio_price > 0 ? formatPrice($book->audio_price) : __("Gratuit") }}</span>
                                    </div>
                                @endif
                            </div>
                            @endif
                            @php
                            $readingProgress = $book->readingProgress->first();
                            $audioProgress = $book->audioProgress->first();

                            $maxProgress = max(
                                $readingProgress ? $readingProgress->progress_percentage : 0,
                                $audioProgress ? $audioProgress->progress_percentage : 0
                            );

                            $actionUrl = route('read.book', $book);
                            $actionIcon = 'fas fa-book-open';
                            $actionText = __('Commencer');

                            if ($maxProgress > 0) {
                                if ($audioProgress && $audioProgress->progress_percentage == $maxProgress) {
                                    $actionUrl = route('listen.book', $book);
                                    $actionIcon = 'fas fa-headphones';
                                    $actionText = $maxProgress < 100 ? __('Continuer l\'écoute') : __('Réécouter');
                                } else {
                                    $actionText = $maxProgress < 100 ? __('Continuer la lecture') : __('Relire');
                                }
                            } elseif ($book->hasPdf()) {
                                $actionText = __('Commencer la lecture');
                            } elseif ($book->hasAudio()) {
                                $actionUrl = route('listen.book', $book);
                                $actionIcon = 'fas fa-headphones';
                                $actionText = __('Commencer l\'écoute');
                            }
                        @endphp
                        
                        @if($maxProgress > 0)
                            <div class="progress" style="height: 5px;" title="{{ round($maxProgress) }}% terminé">
                                <div class="progress-bar" role="progressbar" style="width: {{ $maxProgress }}%;" aria-valuenow="{{ $maxProgress }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        @else
                             <div class="star-rating small mb-2">
                                @php $rating = $book->reviews->avg('rating'); @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $rating ? 'text-warning' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                        @endif

                        <div class="mt-auto pt-2">
                            <a href="{{ $actionUrl }}" class="btn btn-primary btn-sm w-100">
                                <i class="{{ $actionIcon }} me-1"></i> {{ $actionText }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
             <div class="col-12">
                <div class="text-center py-5">
                     <i class="fas fa-search-minus fa-4x text-gray-300 mb-3"></i>
                    <h4>{{ __('Aucun livre ne correspond à vos critères') }}</h4>
                    <p class="text-muted">{{ __('Essayez d\'ajuster vos filtres ou de faire une nouvelle recherche.') }}</p>
                    <a href="{{ route('student.library.index') }}" class="btn btn-primary mt-2">{{ __('Réinitialiser les filtres') }}</a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $books->withQueryString()->links() }}
    </div>
</div>
@endsection