@extends('layouts.app')

@section('content')
    <div class="container-fluid py-3 py-md-4">
        <!-- En-tête : Retour + Favoris -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-6">
                <a href="{{ route('book.show', $book->slug) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> {{ __('Retour') }}
                </a>
            </div>
            <div class="col-md-6 text-md-end">
                @auth
                    <form action="{{ route('favorites.toggle', $book) }}" method="POST" class="d-inline-block favorite-form me-2">
                        @csrf
                        <button type="submit"
                            class="btn {{ auth()->user()->favorites->contains($book->id) ? 'btn-danger' : 'btn-outline-danger' }} btn-sm">
                            <i
                                class="{{ auth()->user()->favorites->contains($book->id) ? 'fas fa-heart' : 'far fa-heart' }} me-1"></i>
                            {{ auth()->user()->favorites->contains($book->id) ? 'Retirer' : 'Favoris' }}
                        </button>
                    </form>
                    @if($canDownload)
                        <a href="{{ route('book.secure_download', $book) }}" class="btn btn-success btn-sm">
                            <i class="bi bi-download me-1"></i> {{ __('Télécharger le PDF') }}
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Titre du livre -->
        <div class="row mb-3">
            <div class="col-12">
                <h1 class="h3 fw-bold text-primary mb-0">{{ $book->title }}</h1>
                @if ($book->author)
                    <p class="text-muted small mb-0">par {{ $book->author->name }}</p>
                @endif
            </div>
        </div>

        <!-- Lecteur PDF -->
        <div class="row">
            <div class="col-12">
                @if ($book->pdf_file)
                    <!-- Zone de chargement -->
                    <div id="loading-indicator" class="text-center py-5 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ __('Chargement du PDF...') }}</span>
                        </div>
                        <p class="mt-2 text-muted">{{ __('Chargement du livre...') }}</p>
                    </div>

                    <!-- Conteneur du PDF -->
                    <div id="pdf-container" class="border rounded shadow-sm bg-white"
                        style="height: 75vh; overflow: auto; display: none;"></div>

                    <!-- Barre de contrôle -->
                    <div id="pdf-controls"
                        class="mt-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 bg-light p-3 rounded shadow-sm"
                        style="display: none;">
                        <!-- Navigation -->
                        <div class="d-flex gap-2">
                            <button id="prev" class="btn btn-outline-secondary" disabled>
                                <i class="bi bi-chevron-left"></i> {{ __('Précédent') }}
                            </button>
                            <button id="next" class="btn btn-outline-secondary" disabled>
                                Suivant <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>

                        <!-- Infos page + progression -->
                        <div class="text-center flex-grow-1">
                            <div class="fw-bold">
                                Page <span id="page-num">1</span> / <span id="page-count">0</span>
                            </div>
                            <div class="progress mt-2" style="height: 6px; width: 200px; margin: 0 auto;">
                                <div id="progress-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%">
                                </div>
                            </div>
                        </div>

                        <!-- Actions supplémentaires -->
                        <div class="d-flex gap-2">
                            <button id="toggle-bookmarks" class="btn btn-outline-info btn-sm" title="{{ __('Marque-pages') }}">
                                <i class="bi bi-bookmarks"></i>
                            </button>
                            <button id="add-bookmark" class="btn btn-outline-success btn-sm" title="{{ __('Ajouter un marque-page') }}">
                                <i class="bi bi-bookmark-plus"></i>
                            </button>
                            <button id="fullscreen" class="btn btn-outline-dark btn-sm" title="{{ __('Plein écran') }}">
                                <i class="bi bi-fullscreen"></i>
                            </button>
                            <button id="zoom-out" class="btn btn-outline-dark btn-sm" title="{{ __('Zoom -') }}">
                                <i class="bi bi-zoom-out"></i>
                            </button>
                            <button id="zoom-in" class="btn btn-outline-dark btn-sm" title="{{ __('Zoom +') }}">
                                <i class="bi bi-zoom-in"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Panneau des marque-pages (initialement caché) -->
                    <div id="bookmarks-panel" class="border-start bg-light p-3"
                        style="position: fixed; top: 0; right: 0; width: 300px; height: 100%; z-index: 1050; transform: translateX(100%); transition: transform 0.3s ease-in-out;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ __('Marque-pages') }}</h5>
                            <button id="close-bookmarks" class="btn-close"></button>
                        </div>
                        <div id="bookmarks-list" class="list-group">
                            <!-- Les marque-pages seront injectés ici par JS -->
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning text-center rounded shadow-sm">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ __('Aucun fichier PDF disponible pour ce livre.') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter/modifier un marque-page -->
    <div class="modal fade" id="bookmark-modal" tabindex="-1" aria-labelledby="bookmarkModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookmark-modal-title">{{ __('Ajouter un marque-page') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bookmark-form">
                        <input type="hidden" id="bookmark-id">
                        <div class="mb-3">
                            <label for="bookmark-title" class="form-label">{{ __('Titre') }}</label>
                            <input type="text" class="form-control" id="bookmark-title" required>
                        </div>
                        <p class="text-muted">Page: <span id="bookmark-page-number"></span></p>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                    <button type="submit" form="bookmark-form" class="btn btn-primary">{{ __('Enregistrer') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- PDF.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Elements du lecteur PDF
            const container = document.getElementById('pdf-container');
            const loading = document.getElementById('loading-indicator');
            const controls = document.getElementById('pdf-controls');
            const pageNumSpan = document.getElementById('page-num');
            const pageCountSpan = document.getElementById('page-count');
            const progressBar = document.getElementById('progress-bar');
            const prevBtn = document.getElementById('prev');
            const nextBtn = document.getElementById('next');
            const fullscreenBtn = document.getElementById('fullscreen');
            const zoomInBtn = document.getElementById('zoom-in');
            const zoomOutBtn = document.getElementById('zoom-out');

            // Elements des marque-pages
            const toggleBookmarksBtn = document.getElementById('toggle-bookmarks');
            const closeBookmarksBtn = document.getElementById('close-bookmarks');
            const bookmarksPanel = document.getElementById('bookmarks-panel');
            const bookmarksList = document.getElementById('bookmarks-list');
            const addBookmarkBtn = document.getElementById('add-bookmark');
            const bookmarkModal = new bootstrap.Modal(document.getElementById('bookmark-modal'));
            const bookmarkForm = document.getElementById('bookmark-form');
            const bookmarkModalTitle = document.getElementById('bookmark-modal-title');
            const bookmarkIdInput = document.getElementById('bookmark-id');
            const bookmarkTitleInput = document.getElementById('bookmark-title');
            const bookmarkPageNumSpan = document.getElementById('bookmark-page-number');

            let pdfDoc = null;

            // Get page from URL query parameter if it exists
            const urlParams = new URLSearchParams(window.location.search);
            const pageFromUrl = parseInt(urlParams.get('page'));

            // Set initial page: URL parameter > DB progress > default 1
            let currentPage = pageFromUrl > 0 ? pageFromUrl : ({{ $initialPage > 1 ? $initialPage : 1 }});

            let totalPages = 0;
            let scale = 1.5;
            let startTime = Date.now();

            const pdfUrl = "{{ route('read.pdf.content', $book) }}?_token={{ $token }}";

            // --- Initialisation du lecteur PDF ---
            loading.classList.remove('d-none');
            fetch(pdfUrl, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('PDF non accessible');
                    return response.arrayBuffer();
                })
                .then(data => pdfjsLib.getDocument({
                    data
                }).promise)
                .then(pdf => {
                    pdfDoc = pdf;
                    totalPages = pdf.numPages;
                    pageCountSpan.textContent = totalPages;
                    updateProgressBar();
                    enableControls();
                    renderPage(currentPage);
                    sendProgressUpdate(currentPage);
                    loadBookmarks(); // Charger les marque-pages après le PDF
                    loading.classList.add('d-none');
                    container.style.display = 'block';
                    controls.style.display = 'flex';
                })
                .catch(err => {
                    console.error(err);
                    container.innerHTML =
                        `<div class="alert alert-danger p-4">{{ __('Erreur : impossible de charger le PDF.') }}</div>`;
                    loading.classList.add('d-none');
                });

            function renderPage(num) {
                if (!pdfDoc || num < 1 || num > totalPages) return;
                pdfDoc.getPage(num).then(page => {
                        const viewport = page.getViewport({
                            scale
                        });
                        const canvas = document.createElement('canvas');
                        const context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        canvas.style.display = 'block';
                        canvas.style.margin = '0 auto';
                        canvas.style.maxWidth = '100%';
                        canvas.classList.add('shadow-sm', 'mb-3', 'rounded');
                        container.innerHTML = '';
                        container.appendChild(canvas);
                        return page.render({
                            canvasContext: context,
                            viewport
                        }).promise;
                    })
                    .then(() => {
                        currentPage = num;
                        pageNumSpan.textContent = currentPage;
                        updateProgressBar();
                        updateNavButtons();
                        container.scrollTop = 0;
                    });
            }

            // --- Contrôles du lecteur PDF ---
            function updateProgressBar() {
                progressBar.style.width = (totalPages > 0 ? (currentPage / totalPages) * 100 : 0) + '%';
            }

            function updateNavButtons() {
                prevBtn.disabled = currentPage <= 1;
                nextBtn.disabled = currentPage >= totalPages;
            }

            function enableControls() {
                prevBtn.disabled = false;
                nextBtn.disabled = false;
            }
            prevBtn.onclick = () => {
                if (currentPage > 1) {
                    sendProgressUpdate();
                    renderPage(currentPage - 1);
                }
            };
            nextBtn.onclick = () => {
                if (currentPage < totalPages) {
                    sendProgressUpdate();
                    renderPage(currentPage + 1);
                }
            };
            document.addEventListener('keydown', e => {
                if (e.key === 'ArrowLeft') prevBtn.click();
                if (e.key === 'ArrowRight') nextBtn.click();
            });
            zoomInBtn.onclick = () => {
                scale = Math.min(scale + 0.25, 3);
                renderPage(currentPage);
            };
            zoomOutBtn.onclick = () => {
                scale = Math.max(scale - 0.25, 0.5);
                renderPage(currentPage);
            };
            fullscreenBtn.onclick = () => {
                if (!document.fullscreenElement) container.requestFullscreen();
                else document.exitFullscreen();
            };
            document.addEventListener('fullscreenchange', () => {
                fullscreenBtn.innerHTML = document.fullscreenElement ?
                    '<i class="bi bi-fullscreen-exit"></i>' : '<i class="bi bi-fullscreen"></i>';
            });

            // --- Logique des Marque-pages ---

            // Afficher/Cacher le panneau
            toggleBookmarksBtn.addEventListener('click', () => bookmarksPanel.style.transform = 'translateX(0)');
            closeBookmarksBtn.addEventListener('click', () => bookmarksPanel.style.transform = 'translateX(100%)');

            // Charger et afficher les marque-pages
            async function loadBookmarks() {
                try {
                    const response = await fetch("{{ route('bookmarks.index', $book) }}");
                    if (!response.ok) throw new Error('Failed to load bookmarks');
                    const bookmarks = await response.json();
                    renderBookmarks(bookmarks);
                } catch (error) {
                    console.error(error);
                    bookmarksList.innerHTML =
                        '<div class="list-group-item text-danger">{{ __('Erreur de chargement.') }}</div>';
                }
            }

            function renderBookmarks(bookmarks) {
                bookmarksList.innerHTML = '';
                if (bookmarks.length === 0) {
                    bookmarksList.innerHTML = '<div class="list-group-item text-muted">{{ __('Aucun marque-page.') }}</div>';
                    return;
                }
                bookmarks.forEach(bm => {
                    const item = document.createElement('div');
                    item.className =
                        'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                    item.innerHTML = `
                                    <div class="flex-grow-1" style="cursor: pointer;">
                                        <div class="fw-bold">${bm.title}</div>
                                        <small class="text-muted">Page ${bm.page_number}</small>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary edit-bookmark-btn" title="{{ __('Modifier') }}"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-outline-danger delete-bookmark-btn" title="{{ __('Supprimer') }}"><i class="bi bi-trash"></i></button>
                                    </div>
                                `;
                    item.querySelector('.flex-grow-1').addEventListener('click', () => {
                        renderPage(bm.page_number);
                        bookmarksPanel.style.transform =
                        'translateX(100%)'; // Cacher le panneau après navigation
                    });
                    item.querySelector('.edit-bookmark-btn').addEventListener('click', () =>
                        openBookmarkModal(bm));
                    item.querySelector('.delete-bookmark-btn').addEventListener('click', () =>
                        deleteBookmark(bm.id));
                    bookmarksList.appendChild(item);
                });
            }

            // Ouvrir le modal pour ajouter ou modifier
            function openBookmarkModal(bookmark = null) {
                bookmarkForm.reset();
                if (bookmark) { // Modification
                    bookmarkModalTitle.textContent = 'Modifier le marque-page';
                    bookmarkIdInput.value = bookmark.id;
                    bookmarkTitleInput.value = bookmark.title;
                    bookmarkPageNumSpan.textContent = bookmark.page_number;
                } else { // Ajout
                    bookmarkModalTitle.textContent = 'Ajouter un marque-page';
                    bookmarkIdInput.value = '';
                    bookmarkPageNumSpan.textContent = currentPage;
                }
                bookmarkModal.show();
            }

            addBookmarkBtn.addEventListener('click', () => openBookmarkModal());

            // Gérer la soumission du formulaire (ajout/modif)
            bookmarkForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const id = bookmarkIdInput.value;
                const title = bookmarkTitleInput.value;
                const isEditing = !!id;

                const url = isEditing ? `/bookmarks/${id}` : "{{ route('bookmarks.store', $book) }}";
                const method = isEditing ? 'PUT' : 'POST';

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            title: title,
                            page_number: currentPage
                        })
                    });
                    if (!response.ok) throw new Error('Save failed');
                    bookmarkModal.hide();
                    loadBookmarks(); // Recharger la liste
                } catch (error) {
                    console.error(error);
                    alert('Erreur lors de l\'enregistrement.');
                }
            });

            // Supprimer un marque-page
            async function deleteBookmark(id) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer ce marque-page ?')) return;
                try {
                    const response = await fetch(`/bookmarks/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    if (!response.ok) throw new Error('Delete failed');
                    loadBookmarks(); // Recharger la liste
                } catch (error) {
                    console.error(error);
                    alert('Erreur lors de la suppression.');
                }
            }

            // --- Progression de lecture ---
            function sendProgressUpdate() {
                const timeSpent = Math.round((Date.now() - startTime) / 1000);
                if (totalPages === 0) return;
                fetch("{{ route('read.progress', $book) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        total_pages: totalPages,
                        current_page: currentPage,
                        time_spent: timeSpent
                    })
                }).finally(() => {
                    startTime = Date.now();
                });
            }
            const interval = setInterval(sendProgressUpdate, 30000);
            window.addEventListener('beforeunload', sendProgressUpdate);
            window.addEventListener('unload', () => clearInterval(interval));
        });
    </script>
@endpush
