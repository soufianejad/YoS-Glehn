@extends('layouts.dashboard')

@section('title', __('Modifier le Livre'))
@section('header', __('Modifier le Livre'))

@section('content')
<div class="container-fluid">
    <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('Informations Principales') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Titre') }}</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $book->title) }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description', $book->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                         <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="author_id" class="form-label">{{ __('Auteur') }}</label>
                                <select class="form-select @error('author_id') is-invalid @enderror" id="author_id" name="author_id" required>
                                    <option value="">{{ __('Sélectionner un Auteur') }}</option>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}" {{ old('author_id', $book->author_id) == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                                    @endforeach
                                </select>
                                @error('author_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">{{ __('Catégorie') }}</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">{{ __('Sélectionner une Catégorie') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $book->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                     <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('Fichiers du Livre') }}</h6>
                    </div>
                    <div class="card-body">
                         <div class="mb-3">
                            <label for="cover_image" class="form-label">{{ __('Image de Couverture') }}</label>
                            @if($book->cover_image_url)
                                <img src="{{ $book->cover_image_url }}" alt="{{ __('Current Cover') }}" class="img-thumbnail mb-2" width="150">
                            @endif
                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image">
                            @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="pdf_file" class="form-label">{{ __('Fichier PDF') }}</label>
                             @if($book->pdf_file) <p><a href="{{ Storage::url($book->pdf_file) }}" target="_blank">{{ __('PDF Actuel') }}</a></p> @endif
                            <input type="file" class="form-control @error('pdf_file') is-invalid @enderror" id="pdf_file" name="pdf_file">
                            @error('pdf_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="audio_file" class="form-label">{{ __('Fichier Audio') }}</label>
                             @if($book->audio_file) <p><a href="{{ Storage::url($book->audio_file) }}" target="_blank">{{ __('Audio Actuel') }}</a></p> @endif
                            <input type="file" class="form-control @error('audio_file') is-invalid @enderror" id="audio_file" name="audio_file">
                            @error('audio_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Column -->
            <div class="col-lg-4">
                 <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('Publication') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">{{ __('Statut') }}</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                               <option value="draft" {{ old('status', $book->status) == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                <option value="pending" {{ old('status', $book->status) == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="published" {{ old('status', $book->status) == 'published' ? 'selected' : '' }}>Publié</option>
                                <option value="rejected" {{ old('status', $book->status) == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                                <option value="archived" {{ old('status', $book->status) == 'archived' ? 'selected' : '' }}>Archivé</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                         <div class="mb-3">
                            <label for="space" class="form-label">{{ __('Espace') }}</label>
                            <select class="form-select @error('space') is-invalid @enderror" id="space" name="space" required>
                                <option value="public" {{ old('space', $book->space) == 'public' ? 'selected' : '' }}>Public</option>
                                <option value="educational" {{ old('space', $book->space) == 'educational' ? 'selected' : '' }}>Éducatif</option>
                                <option value="adult" {{ old('space', $book->space) == 'adult' ? 'selected' : '' }}>Adulte</option>
                            </select>
                            @error('space')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3 form-check form-switch">
                            <input type="hidden" name="is_downloadable" value="0">
                            <input class="form-check-input" type="checkbox" id="is_downloadable" name="is_downloadable" value="1" {{ old('is_downloadable', $book->is_downloadable) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_downloadable">{{ __('Téléchargeable') }}</label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">{{ __('Mettre à jour le Livre') }}</button>
                            <a href="{{ route('admin.books.index') }}" class="btn btn-secondary mt-2">{{ __('Annuler') }}</a>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('Métadonnées') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="isbn" class="form-label">ISBN</label>
                            <input type="text" class="form-control @error('isbn') is-invalid @enderror" id="isbn" name="isbn" value="{{ old('isbn', $book->isbn) }}">
                            @error('isbn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="published_year" class="form-label">{{ __('Année de Publication') }}</label>
                            <input type="number" class="form-control @error('published_year') is-invalid @enderror" id="published_year" name="published_year" value="{{ old('published_year', $book->published_year) }}">
                            @error('published_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                         <div class="mb-3">
                            <label for="language" class="form-label">{{ __('Langue') }}</label>
                            <input type="text" class="form-control @error('language') is-invalid @enderror" id="language" name="language" value="{{ old('language', $book->language) }}">
                            @error('language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                         <div class="mb-3">
                            <label for="pdf_pages" class="form-label">{{ __('Pages (PDF)') }}</label>
                            <input type="number" class="form-control @error('pdf_pages') is-invalid @enderror" id="pdf_pages" name="pdf_pages" value="{{ old('pdf_pages', $book->pdf_pages) }}">
                            @error('pdf_pages')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="audio_duration" class="form-label">{{ __('Durée Audio (secondes)') }}</label>
                            <input type="number" class="form-control @error('audio_duration') is-invalid @enderror" id="audio_duration" name="audio_duration" value="{{ old('audio_duration', $book->audio_duration) }}">
                            @error('audio_duration')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                 <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('Tarification') }}</h6>
                    </div>
                     <div class="card-body">
                         <div class="mb-3">
                            <label for="content_type" class="form-label">{{ __('Type de Contenu') }}</label>
                            <select class="form-select @error('content_type') is-invalid @enderror" id="content_type" name="content_type" required>
                                <option value="free" {{ old('content_type', $book->content_type) == 'free' ? 'selected' : '' }}>Gratuit</option>
                                <option value="premium" {{ old('content_type', $book->content_type) == 'premium' ? 'selected' : '' }}>Premium</option>
                            </select>
                            @error('content_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="pdf_price" class="form-label">{{ __('Prix PDF') }}</label>
                            <input type="number" step="0.01" class="form-control @error('pdf_price') is-invalid @enderror" id="pdf_price" name="pdf_price" value="{{ old('pdf_price', $book->pdf_price) }}">
                            @error('pdf_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                         <div class="mb-3">
                            <label for="audio_price" class="form-label">{{ __('Prix Audio') }}</label>
                            <input type="number" step="0.01" class="form-control @error('audio_price') is-invalid @enderror" id="audio_price" name="audio_price" value="{{ old('audio_price', $book->audio_price) }}">
                            @error('audio_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
