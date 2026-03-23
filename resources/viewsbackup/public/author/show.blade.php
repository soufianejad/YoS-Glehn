@extends('layouts.app')

@section('title', __('Profil de') . ' ' . $author->name . ' - ' . config('platform.name'))

@push('styles')
<style>
    .author-header {
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1506784983877-45594efa4c88?auto=format&fit=crop&w=1770&q=80') no-repeat center center;
        background-size: cover;
        padding: 4rem 0;
        color: white;
    }
    .author-avatar-large {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 5px solid white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .book-card .card-img-top {
        height: 300px;
        object-fit: cover;
    }
    .star-rating {
        color: #ffc107;
    }
</style>
@endpush

@section('content')

<!-- Author Header -->
<section class="author-header text-center">
    <div class="container">
        <img src="{{ $author->avatar_url }}" alt="{{ $author->name }}" class="author-avatar-large mb-3">
        <h1 class="display-4 font-weight-bold">{{ $author->name }}</h1>
        <p class="lead">{{ __('Auteur sur la plateforme') }}</p>
    </div>
</section>

<div class="container py-5">

    <!-- Author's Books Section -->
    <section>
        <h2 class="section-title text-center mb-5">{{ __('Les Livres de') }} {{ $author->name }}</h2>
        <div class="row">
            @forelse($author->books as $book)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card book-card h-100 shadow-sm border-0">
                        <a href="{{ route('book.show', $book->slug) }}">
                            <img src="{{ $book->cover_image_url ?? 'https://via.placeholder.com/300x400' }}" class="card-img-top" alt="{{ $book->title }}">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><a href="{{ route('book.show', $book->slug) }}" class="text-dark">{{ Str::limit($book->title, 50) }}</a></h5>
                            <div class="star-rating mb-3">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $book->average_rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                @endfor
                                <span class="text-muted small">({{ $book->reviews_count }})</span>
                            </div>
                            <p class="card-text small">{{ Str::limit($book->description, 100) }}</p>
                            <a href="{{ route('book.show', $book->slug) }}" class="btn btn-primary mt-auto">{{ __('Voir Détails') }}</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted">{{ $author->name }} {{__('n\'a pas encore publié de livres.')}}</p>
                </div>
            @endforelse
        </div>
    </section>

</div>
@endsection
