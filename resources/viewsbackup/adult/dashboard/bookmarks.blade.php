@extends('layouts.dashboard')

@section('title', 'Mes Favoris')
@section('header', 'Mes Favoris (Adulte)')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Livres mis en favoris</h6>
        </div>
        <div class="card-body">
            @if($bookmarks->isEmpty())
                <p class="text-center text-muted">Vous n'avez aucun livre en favori dans cette section.</p>
            @else
                <div class="list-group">
                    @foreach($bookmarks as $bookmark)
                        <a href="{{ route('adult.library.show', $bookmark->book->slug) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1">{{ $bookmark->book->title }}</h5>
                                <small>Ajouté le {{ $bookmark->created_at->format('d/m/Y') }}</small>
                            </div>
                            <p class="mb-1">{{ Str::limit($bookmark->book->description, 150) }}</p>
                            <small>par {{ $bookmark->book->author->name ?? 'N/A' }}</small>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="card-footer">
            {{ $bookmarks->links() }}
        </div>
    </div>
</div>
@endsection
