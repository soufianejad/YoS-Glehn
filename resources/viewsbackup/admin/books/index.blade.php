@extends('layouts.dashboard')

@section('title', __('Gestion des Livres'))
@section('header', __('Gestion des Livres'))

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold ">{{ __('Filtres et Recherche') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.books.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label for="search" class="form-label">{{ __('Rechercher (Titre, ISBN)') }}</label>
                        <input type="text" name="search" id="search" class="form-control" value="{{ $search ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label for="author" class="form-label">{{ __('Auteur') }}</label>
                        <select name="author" id="author" class="form-select">
                            <option value="">{{ __('Tous') }}</option>
                            @foreach($authors as $author)
                                <option value="{{ $author->id }}" {{ $authorFilter == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="category" class="form-label">{{ __('Catégorie') }}</label>
                        <select name="category" id="category" class="form-select">
                            <option value="">{{ __('Toutes') }}</option>
                             @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $categoryFilter == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label for="status" class="form-label">{{ __('Statut') }}</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">{{ __('Tous') }}</option>
                            <option value="published" {{ $statusFilter == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="pending" {{ $statusFilter == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="draft" {{ $statusFilter == 'draft' ? 'selected' : '' }}>Draft</option>
                             <option value="rejected" {{ $statusFilter == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                     <div class="col-md-1">
                        <label for="space" class="form-label">{{ __('Espace') }}</label>
                        <select name="space" id="space" class="form-select">
                            <option value="">{{ __('Tous') }}</option>
                            <option value="public" {{ $spaceFilter == 'public' ? 'selected' : '' }}>Public</option>
                            <option value="educational" {{ $spaceFilter == 'educational' ? 'selected' : '' }}>Educational</option>
                            <option value="adult" {{ $spaceFilter == 'adult' ? 'selected' : '' }}>Adult</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-primary">{{ __('Filtrer') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold ">{{ __('Liste des Livres') }} ({{ $books->total() }})</h6>
            <a href="{{ route('admin.books.create') }}" class="btn btn-primary btn-icon-split btn-sm">
                <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                <span class="text">{{ __('Nouveau Livre') }}</span>
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('Livre') }}</th>
                            <th>{{ __('Auteur') }}</th>
                            <th>{{ __('Catégorie') }}</th>
                            <th class="text-center">{{ __('Statut') }}</th>
                            <th class="text-center">{{ __('Espace') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}" class="rounded mr-3" style="width: 50px; height: 70px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 font-weight-bold">{{ $book->title }}</h6>
                                            <small class="text-muted">ISBN: {{ $book->isbn ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $book->author->name ?? 'N/A' }}</td>
                                <td>{{ $book->category->name ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <span class="badge-{{ \App\Helpers\StatusHelper::bookStatusColor($book->status) }}">{{ ucfirst($book->status) }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-secondary">{{ ucfirst($book->space) }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.books.show', $book) }}" class="btn btn-info btn-circle btn-sm" title="{{__('Voir')}}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-warning btn-circle btn-sm" title="{{__('Modifier')}}">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <a href="{{ route('admin.quiz.create', $book) }}" class="btn btn-primary btn-circle btn-sm" title="Créer un Quiz">
                                        <i class="fas fa-plus-square"></i>
                                    </a>
                                    @if($book->status == 'pending')
                                        <form action="{{ route('admin.books.approve', $book) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-circle btn-sm" title="{{__('Approuver')}}"><i class="fas fa-check"></i></button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.books.destroy', $book) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-circle btn-sm" onclick="return confirm('{{ __('Êtes-vous sûr ?') }}')" title="{{__('Supprimer')}}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">{{ __('Aucun livre trouvé.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="d-flex justify-content-center mt-3">
                {{ $books->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
