@extends('layouts.dashboard')

@section('title', __('Créer un Livre'))
@section('header', __('Créer un Nouveau Livre'))

@section('content')
<div class="container-fluid">
    <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
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
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                         <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="author_id" class="form-label">{{ __('Auteur') }}</label>
                                <select class="form-select @error('author_id') is-invalid @enderror" id="author_id" name="author_id" required>
                                    <option value="">{{ __('Sélectionner un Auteur') }}</option>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                                    @endforeach
                                </select>
                                @error('author_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">{{ __('Catégorie') }}</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">{{ __('Sélectionner une Catégorie') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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
                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image" accept="image/*">
                            @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="pdf_file" class="form-label">{{ __('Fichier PDF') }}</label>
                            <input type="file" class="form-control @error('pdf_file') is-invalid @enderror" id="pdf_file" name="pdf_file" accept="application/pdf">
                            @error('pdf_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="audio_file" class="form-label">{{ __('Fichier Audio') }}</label>
                            <input type="file" class="form-control @error('audio_file') is-invalid @enderror" id="audio_file" name="audio_file" accept="audio/*">
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
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publié</option>
                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archivé</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                         <div class="mb-3">
                            <label for="space" class="form-label">{{ __('Espace') }}</label>
                            <select class="form-select @error('space') is-invalid @enderror" id="space" name="space" required>
                                <option value="public" {{ old('space') == 'public' ? 'selected' : '' }}>Public</option>
                                <option value="educational" {{ old('space') == 'educational' ? 'selected' : '' }}>Éducatif</option>
                                <option value="adult" {{ old('space') == 'adult' ? 'selected' : '' }}>Adulte</option>
                            </select>
                            @error('space')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">{{ __('Créer le Livre') }}</button>
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
                            <input type="text" class="form-control @error('isbn') is-invalid @enderror" id="isbn" name="isbn" value="{{ old('isbn') }}">
                            @error('isbn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="published_year" class="form-label">{{ __('Année de Publication') }}</label>
                            <input type="number" class="form-control @error('published_year') is-invalid @enderror" id="published_year" name="published_year" value="{{ old('published_year') }}">
                            @error('published_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                         <div class="mb-3">
                            <label for="language" class="form-label">{{ __('Langue') }}</label>
                            <input type="text" class="form-control @error('language') is-invalid @enderror" id="language" name="language" value="{{ old('language') }}">
                            @error('language')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                         <div class="mb-3">
                            <label for="pdf_pages" class="form-label">{{ __('Pages (PDF)') }}</label>
                            <input type="number" class="form-control @error('pdf_pages') is-invalid @enderror" id="pdf_pages" name="pdf_pages" value="{{ old('pdf_pages') }}">
                            @error('pdf_pages')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="audio_duration" class="form-label">{{ __('Durée Audio (secondes)') }}</label>
                            <input type="number" class="form-control @error('audio_duration') is-invalid @enderror" id="audio_duration" name="audio_duration" value="{{ old('audio_duration') }}">
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
                                <option value="free" {{ old('content_type') == 'free' ? 'selected' : '' }}>Gratuit</option>
                                <option value="premium" {{ old('content_type') == 'premium' ? 'selected' : '' }}>Premium</option>
                            </select>
                            @error('content_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="pdf_price" class="form-label">{{ __('Prix PDF') }}</label>
                            <input type="number" step="0.01" class="form-control @error('pdf_price') is-invalid @enderror" id="pdf_price" name="pdf_price" value="{{ old('pdf_price') }}">
                            @error('pdf_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                         <div class="mb-3">
                            <label for="audio_price" class="form-label">{{ __('Prix Audio') }}</label>
                            <input type="number" step="0.01" class="form-control @error('audio_price') is-invalid @enderror" id="audio_price" name="audio_price" value="{{ old('audio_price') }}">
                            @error('audio_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // PDF Page Count
        const pdfInput = document.getElementById('pdf_file');
        const pagesInput = document.getElementById('pdf_pages');
        pdfInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file && file.type === 'application/pdf') {
                const fileReader = new FileReader();
                fileReader.onload = function () {
                    const typedarray = new Uint8Array(this.result);
                    // Use the PDF.js library
                    pdfjsLib.getDocument(typedarray).promise.then(function (pdf) {
                        pagesInput.value = pdf.numPages;
                    }).catch(function(error) {
                        console.error('Error parsing PDF:', error);
                        alert('Could not read the PDF file to count pages.');
                    });
                };
                fileReader.readAsArrayBuffer(file);
            }
        });

        // Audio Duration
        const audioInput = document.getElementById('audio_file');
        const durationInput = document.getElementById('audio_duration');
        audioInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file && file.type.startsWith('audio/')) {
                const audio = document.createElement('audio');
                audio.src = URL.createObjectURL(file);
                audio.addEventListener('loadedmetadata', function () {
                    durationInput.value = Math.round(audio.duration);
                    URL.revokeObjectURL(audio.src); // Clean up memory
                });
                audio.addEventListener('error', function() {
                    console.error('Error loading audio file.');
                    alert('Could not read the audio file to get its duration.');
                });
            }
        });
    });
</script>
@endpush
