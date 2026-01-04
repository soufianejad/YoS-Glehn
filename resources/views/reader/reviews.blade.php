@extends('layouts.dashboard')

@section('title', 'Mes Avis')
@section('header', 'Mes Avis')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Avis que j'ai laissés</h6>
        </div>
        <div class="card-body">
            @if($reviews->isEmpty())
                <p class="text-center text-muted py-4">Vous n'avez laissé aucun avis pour le moment.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Livre</th>
                                <th>Note</th>
                                <th>Commentaire</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reviews as $review)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $review->book->cover_image_url }}" alt="{{ $review->book->title }}" class="rounded me-3" style="width: 50px; height: 70px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 font-weight-bold">{{ $review->book->title }}</h6>
                                                <small class="text-muted">par {{ $review->book->author->name ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-warning">
                                            @for ($i = 0; $i < 5; $i++)
                                                <i class="fas fa-star{{ $i < $review->rating ? '' : '-regular' }}"></i>
                                            @endfor
                                            <span class="ms-1">({{ $review->rating }}/5)</span>
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($review->comment, 100) }}</td>
                                    <td>{{ $review->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
