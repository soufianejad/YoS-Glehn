@extends('layouts.dashboard')

@section('title', 'Mes Favoris')
@section('header', 'Mes Favoris')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Livres mis en favoris</h6>
        </div>
        <div class="card-body">
            @if($bookmarks->isEmpty())
                <p class="text-center text-muted py-4">Vous n'avez aucun livre en favori pour le moment.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Livre</th>
                                <th>Description</th>
                                <th>Date d'ajout</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookmarks as $bookmark)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $bookmark->book->cover_image_url }}" alt="{{ $bookmark->book->title }}" class="rounded me-3" style="width: 50px; height: 70px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 font-weight-bold">{{ $bookmark->book->title }}</h6>
                                                <small class="text-muted">par {{ $bookmark->book->author->name ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($bookmark->book->description, 100) }}</td>
                                    <td>{{ $bookmark->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('reader.library.show', $bookmark->book->slug) }}" class="btn btn-info btn-circle btn-sm" title="Voir le livre">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        {{-- Assuming a route for deleting bookmarks exists --}}
                                        {{-- <form action="{{ route('reader.bookmarks.destroy', $bookmark) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-circle btn-sm" onclick="return confirm('Êtes-vous sûr ?')" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form> --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $bookmarks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
