@extends('layouts.dashboard')

@section('title', 'Mes Avis')
@section('header', 'Mes Avis (Adulte)')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Avis que j'ai laissés</h6>
        </div>
        <div class="card-body">
            @if($reviews->isEmpty())
                <p class="text-center text-muted">Vous n'avez laissé aucun avis sur les livres de cette section.</p>
            @else
                @foreach($reviews as $review)
                    <div class="d-flex mb-3 border-bottom pb-3">
                        <img src="{{ $review->book->cover_image_url }}" class="rounded" style="width: 60px; height: 90px; object-fit: cover;" alt="{{ $review->book->title }}">
                        <div class="ms-3">
                            <h6 class="mb-0"><a href="{{ route('adult.library.show', $review->book->slug) }}">{{ $review->book->title }}</a></h6>
                            <div class="text-warning">
                                @for ($i = 0; $i < 5; $i++)
                                    <i class="fas fa-star{{ $i < $review->rating ? '' : '-regular' }}"></i>
                                @endfor
                                <span class="text-muted ms-1">({{ $review->rating }}/5)</span>
                            </div>
                            <p class="mt-1 mb-0">{{ $review->comment }}</p>
                            <small class="text-muted">Posté le {{ $review->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        <div class="card-footer">
            {{ $reviews->links() }}
        </div>
    </div>
</div>
@endsection
