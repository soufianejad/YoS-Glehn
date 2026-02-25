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
                            @if($book->cover_image)
                                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ __('Current Cover') }}" class="img-thumbnail mb-2" width="150">
                            @endif
                            <input type="file" class="form-control @error('cover_image') is-invalid @enderror" id="cover_image" name="cover_image">
                            @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="pdf_file" class="form-label">{{ __('Fichier PDF Principal') }}</label>
                             @if($book->pdf_file) <p><a href="{{ Storage::url($book->pdf_file) }}" target="_blank">{{ __('PDF Actuel') }}</a></p> @endif
                            <input type="file" class="form-control @error('pdf_file') is-invalid @enderror" id="pdf_file" name="pdf_file">
                            @error('pdf_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="audio_file" class="form-label">{{ __('Fichier Audio Principal (optionnel)') }}</label>
                             @if($book->audio_file) <p><a href="{{ Storage::url($book->audio_file) }}" target="_blank">{{ __('Audio Actuel') }}</a></p> @endif
                            <input type="file" class="form-control @error('audio_file') is-invalid @enderror" id="audio_file" name="audio_file">
                            @error('audio_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Dynamic File Uploads for PDF and Audio -->
                        <div id="book-files-container">
                            <h6 class="mt-4 mb-3">{{ __('Fichiers Multilingues (PDF & Audio)') }}</h6>
                            @foreach($book->files as $index => $file)
                                <div class="book-file-entry border p-3 mb-3 rounded" data-file-id="{{ $file->id }}">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Langue') }}</label>
                                            <select name="existing_files[{{ $file->id }}][language]" class="form-select language-select" required>
                                                <option value="">{{ __('Sélectionner une langue') }}</option>
                                                @foreach($languages as $lang)
                                                    <option value="{{ $lang['code'] }}" {{ $file->language == $lang['code'] ? 'selected' : '' }}>{{ $lang['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error("existing_files.{$file->id}.language")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Fichier PDF (optionnel)') }}</label>
                                            @if($file->file_type == 'pdf' && $file->path)
                                                <p><a href="{{ Storage::url($file->path) }}" target="_blank">{{ __('PDF Actuel') }}</a></p>
                                            @endif
                                            <input type="file" name="existing_files[{{ $file->id }}][pdf_file]" class="form-control pdf-file-input" accept="application/pdf">
                                            @error("existing_files.{$file->id}.pdf_file")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            <div class="mt-2">
                                                <label class="form-label">{{ __('Nbre de Pages (PDF)') }}</label>
                                                <input type="number" name="existing_files[{{ $file->id }}][pdf_pages]" class="form-control pdf-pages-input" min="1" value="{{ $file->file_type == 'pdf' ? $file->pages : '' }}">
                                                @error("existing_files.{$file->id}.pdf_pages")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ __('Fichier Audio (optionnel)') }}</label>
                                            @if($file->file_type == 'audio' && $file->path)
                                                <p><a href="{{ Storage::url($file->path) }}" target="_blank">{{ __('Audio Actuel') }}</a></p>
                                            @endif
                                            <input type="file" name="existing_files[{{ $file->id }}][audio_file]" class="form-control audio-file-input" accept="audio/*">
                                            @error("existing_files.{$file->id}.audio_file")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            <div class="mt-2">
                                                <label class="form-label">{{ __('Durée Audio (secondes)') }}</label>
                                                <input type="number" name="existing_files[{{ $file->id }}][audio_duration]" class="form-control audio-duration-input" min="1" value="{{ $file->file_type == 'audio' ? $file->duration : '' }}">
                                                @error("existing_files.{$file->id}.audio_duration")<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm remove-file-entry mt-2" data-file-id="{{ $file->id }}">{{ __('Supprimer ce fichier') }}</button>
                                </div>
                            @endforeach
                            <div id="new-book-files-container">
                                <!-- New file entries will be added here -->
                            </div>
                        </div>
                        <button type="button" id="add-file-entry" class="btn btn-secondary mt-3">{{ __('Ajouter un autre fichier') }}</button>
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

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let fileEntryIndex = {{ count($book->files) }}; // Start index after existing files

        function initializeFileEntry(entryElement) {
            const pdfInput = entryElement.querySelector('.pdf-file-input');
            const pdfPagesInput = entryElement.querySelector('.pdf-pages-input');
            const audioInput = entryElement.querySelector('.audio-file-input');
            const audioDurationInput = entryElement.querySelector('.audio-duration-input');

            // PDF Page Count
            if (pdfInput && pdfPagesInput) {
                pdfInput.addEventListener('change', function (event) {
                    const file = event.target.files[0];
                    if (file && file.type === 'application/pdf') {
                        const fileReader = new FileReader();
                        fileReader.onload = function () {
                            const typedarray = new Uint8Array(this.result);
                            pdfjsLib.getDocument(typedarray).promise.then(function (pdf) {
                                pdfPagesInput.value = pdf.numPages;
                            }).catch(function(error) {
                                console.error('Error parsing PDF:', error);
                                alert('Could not read the PDF file to count pages.');
                            });
                        };
                        fileReader.readAsArrayBuffer(file);
                    } else {
                        pdfPagesInput.value = ''; // Clear if not a PDF
                    }
                });
            }

            // Audio Duration
            if (audioInput && audioDurationInput) {
                audioInput.addEventListener('change', function (event) {
                    const file = event.target.files[0];
                    if (file && file.type.startsWith('audio/')) {
                        const audio = document.createElement('audio');
                        audio.src = URL.createObjectURL(file);
                        audio.addEventListener('loadedmetadata', function () {
                            audioDurationInput.value = Math.round(audio.duration);
                            URL.revokeObjectURL(audio.src); // Clean up memory
                        });
                        audio.addEventListener('error', function() {
                            console.error('Error loading audio file.');
                            alert('Could not read the audio file to get its duration.');
                        });
                    } else {
                        audioDurationInput.value = ''; // Clear if not audio
                    }
                });
            }

            // Remove entry
            const removeButton = entryElement.querySelector('.remove-file-entry');
            if (removeButton) {
                removeButton.addEventListener('click', function () {
                    const fileId = this.dataset.fileId;
                    if (fileId) {
                        // If it's an existing file, mark for deletion (send to server)
                        const deleteInput = document.createElement('input');
                        deleteInput.type = 'hidden';
                        deleteInput.name = `deleted_files[]`;
                        deleteInput.value = fileId;
                        entryElement.appendChild(deleteInput);
                    }
                    entryElement.remove();
                });
            }
        }

        // Initialize existing entries
        document.querySelectorAll('.book-file-entry').forEach(initializeFileEntry);

        // Add new file entry
        document.getElementById('add-file-entry').addEventListener('click', function () {
            const container = document.getElementById('new-book-files-container');
            const newEntry = document.createElement('div');
            newEntry.classList.add('book-file-entry', 'border', 'p-3', 'mb-3', 'rounded');
            newEntry.innerHTML = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Langue') }}</label>
                        <select name="new_files[${fileEntryIndex}][language]" class="form-select language-select" required>
                            <option value="">{{ __('Sélectionner une langue') }}</option>
                            @foreach($languages as $lang)
                                <option value="{{ $lang['code'] }}">{{ $lang['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Fichier PDF (optionnel)') }}</label>
                        <input type="file" name="new_files[${fileEntryIndex}][pdf_file]" class="form-control pdf-file-input" accept="application/pdf">
                        <div class="mt-2">
                            <label class="form-label">{{ __('Nbre de Pages (PDF)') }}</label>
                            <input type="number" name="new_files[${fileEntryIndex}][pdf_pages]" class="form-control pdf-pages-input" min="1">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Fichier Audio (optionnel)') }}</label>
                        <input type="file" name="new_files[${fileEntryIndex}][audio_file]" class="form-control audio-file-input" accept="audio/*">
                        <div class="mt-2">
                            <label class="form-label">{{ __('Durée Audio (secondes)') }}</label>
                            <input type="number" name="new_files[${fileEntryIndex}][audio_duration]" class="form-control audio-duration-input" min="1">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-file-entry mt-2">{{ __('Supprimer') }}</button>
            `;
            container.appendChild(newEntry);
            initializeFileEntry(newEntry);
            fileEntryIndex++;
        });
    });
</script>
@endpush