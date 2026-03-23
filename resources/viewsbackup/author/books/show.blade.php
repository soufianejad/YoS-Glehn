@extends('layouts.author')

@section('title', 'Tableau de bord du livre')

@php
    function render_stars($rating, $max = 5) {
        $full_star = '<i class="fas fa-star text-warning"></i>';
        $empty_star = '<i class="far fa-star text-warning"></i>';
        $stars = '';
        $rating = floatval($rating);
        for ($i = 1; $i <= $max; $i++) {
            $stars .= $rating >= $i ? $full_star : $empty_star;
        }
        return $stars;
    }
@endphp

@section('header', 'Tableau de bord du livre')

@section('content')
<div class="container-fluid">

    <!-- Book Header -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 text-center">
                    <img src="{{ $book->cover_image_url }}" class="img-fluid rounded" alt="{{ $book->title }}" style="max-height: 200px;">
                </div>
                <div class="col-md-10">
                    <span class="badge badge-{{ \App\Helpers\StatusHelper::bookStatusColor($book->status) }} mb-2">{{ ucfirst($book->status) }}</span>
                    <h1 class="h3 font-weight-bold mb-1">{{ $book->title }}</h1>
                    <p class="text-muted">{{ $book->category->name ?? 'N/A' }}</p>
                    <div class="mt-3">
                        <a href="{{ route('author.books.edit', $book) }}" class="btn btn-warning">
                            <i class="fas fa-pencil-alt me-1"></i> Modifier le livre
                        </a>
                        <a href="{{ route('author.books.statistics', $book) }}" class="btn btn-primary">
                            <i class="fas fa-chart-line me-1"></i> Voir les statistiques détaillées
                        </a>
                        <a href="{{ route('author.books.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-left-primary shadow py-2">
                <div class="card-body"><div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Revenus</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['revenue'], 2) }} €</div></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-left-success shadow py-2">
                <div class="card-body"><div class="text-xs font-weight-bold text-success text-uppercase mb-1">Ventes</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['sales'] }}</div></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-left-info shadow py-2">
                <div class="card-body"><div class="text-xs font-weight-bold text-info text-uppercase mb-1">Note Moyenne</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800 d-flex align-items-center">
                    {{ number_format($stats['avg_rating'], 2) }} <span class="ms-2">{!! render_stars($stats['avg_rating']) !!}</span>
                </div></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card h-100 border-left-warning shadow py-2">
                <div class="card-body"><div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Avis</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['reviews_count'] }}</div></div>
            </div>
        </div>
    </div>
    
    <!-- Details & Reviews -->
    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Description</h6></div>
                <div class="card-body">
                    <p>{{ $book->description }}</p>
                </div>
            </div>
             <div class="card shadow-sm">
                <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Métadonnées</h6></div>
                <div class="card-body">
                    <p><strong>ISBN:</strong> {{ $book->isbn ?? 'N/A' }}</p>
                    <p><strong>Année de Publication:</strong> {{ $book->published_year ?? 'N/A' }}</p>
                    <p><strong>Langue:</strong> {{ $book->language ?? 'N/A' }}</p>
                    <p><strong>Espace:</strong> {{ ucfirst($book->space) }}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Derniers Avis</h6></div>
                <div class="card-body">
                    @forelse($recentReviews as $review)
                        <div class="d-flex mb-3 @if(!$loop->last) border-bottom pb-3 @endif">
                            <img src="{{ $review->user->avatar_url ?? asset('images/default-avatar.png') }}" class="rounded-circle" style="width: 40px; height: 40px;" alt="{{ $review->user->name }}">
                            <div class="ms-3">
                                <h6 class="mb-0">{{ $review->user->name }}</h6>
                                <div class="small mb-1">{!! render_stars($review->rating) !!}</div>
                                <p class="mb-0 small">{{ $review->comment }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">Aucun avis pour ce livre pour le moment.</p>
                    @endforelse
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('author.reviews') }}">Voir tous les avis</a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
