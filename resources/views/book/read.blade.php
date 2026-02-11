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
                    <div id="loading-indicator" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ __('Chargement du livre...') }}</span>
                        </div>
                        <p class="mt-2 text-muted">{{ __('Préparation du livre, cela peut prendre un moment...') }}</p>
                    </div>

                    <!-- Conteneur Flipbook -->
                    <div class="flipbook-viewport d-none">
                        <div class="container">
                            <div class="flipbook" id="flipbook"></div>
                        </div>
                    </div>


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

@push('styles')
<style>
.flipbook-viewport {
    overflow: hidden;
    width: 100%;
    height: 80vh;
}
.flipbook-viewport .container {
    position: absolute;
    top: 50%;
    left: 50%;
    margin: auto;
}
.flipbook {
    width: 922px;
    height: 600px;
    left: -461px;
    top: -300px;
}
.flipbook .page {
    width: 461px;
    height: 600px;
    background-color: white;
    background-repeat: no-repeat;
    background-size: 100% 100%;
}
.flipbook .page canvas {
    width: 100%;
    height: 100%;
}
.flipbook .shadow,
.flipbook .gradient {
    position: absolute;
    top: 0;
    height: 100%;
    z-index: 2;
}
</style>
@endpush

@push('scripts')
    {{-- PDF.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
    </script>
    {{-- jQuery (required by turn.js) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- turn.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/turn.js/4.1.0/turn.min.js"></script>
    
    <script>
        // Note: turn.js requires jQuery.
        $(function() {
            const loading = document.getElementById('loading-indicator');
            const flipbookContainer = document.querySelector('.flipbook-viewport');
            const flipbook = $('#flipbook');
            const controls = document.getElementById('pdf-controls');
            const pageNumSpan = document.getElementById('page-num');
            const pageCountSpan = document.getElementById('page-count');
            const progressBar = document.getElementById('progress-bar');
            const prevBtn = document.getElementById('prev');
            const nextBtn = document.getElementById('next');

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
            let totalPages = 0;
            let currentPage = 1;
            let startTime = Date.now();

            const pdfUrl = "{{ route('read.pdf.content', $book) }}?_token={{ $token }}";

            // --- PDF Loading ---
            pdfjsLib.getDocument(pdfUrl).promise.then(pdf => {
                pdfDoc = pdf;
                totalPages = pdf.numPages;
                pageCountSpan.textContent = totalPages;
                
                // Initialize flipbook
                initializeFlipbook();

                loading.classList.add('d-none');
                flipbookContainer.classList.remove('d-none');
                controls.style.display = 'flex';
                
            }).catch(err => {
                console.error("PDF loading error:", err);
                loading.innerHTML = `<div class="alert alert-danger">{{ __('Erreur de chargement du PDF.') }}</div>`;
            });


            function initializeFlipbook() {
                flipbook.turn({
                    width: 922,
                    height: 600,
                    elevation: 50,
                    gradients: true,
                    autoCenter: true,
                    page: {{ $initialPage > 1 ? $initialPage : 1 }},
                    pages: totalPages,
                    when: {
                        turning: function(event, page, view) {
                            // Pre-render pages that are about to be shown
                            for (let i = 0; i < view.length; i++) {
                                if (view[i] !== 0) {
                                    renderPageInto(view[i]);
                                }
                            }
                        },
                        turned: function(event, page, view) {
                            currentPage = page;
                            pageNumSpan.textContent = page;
                            updateProgressBar();
                            sendProgressUpdate();
                        }
                    }
                });

                // Add placeholders for all pages
                for (let i = 1; i <= totalPages; i++) {
                    const pageElement = $(`<div id="page-container-${i}" />`);
                    flipbook.turn('addPage', pageElement, i);
                }

                loadBookmarks();
            }

            // --- Page Rendering ---
            function renderPageInto(pageNumber) {
                const container = document.getElementById(`page-container-${pageNumber}`);

                // If page is already rendered or rendering, skip.
                if (!container || container.hasAttribute('data-rendered')) {
                    return;
                }
                container.setAttribute('data-rendered', 'true'); // Mark as rendering

                pdfDoc.getPage(pageNumber).then(page => {
                    const viewport = page.getViewport({ scale: 1 });
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    
                    // Adjust canvas size to match the desired page size of the flipbook
                    const scale = Math.min(461 / viewport.width, 600 / viewport.height);
                    const scaledViewport = page.getViewport({ scale });
                    
                    canvas.height = scaledViewport.height;
                    canvas.width = scaledViewport.width;

                    container.innerHTML = '';
                    container.appendChild(canvas);

                    page.render({
                        canvasContext: context,
                        viewport: scaledViewport
                    });
                });
            }

            // --- Controls ---
             function updateProgressBar() {
                progressBar.style.width = (totalPages > 0 ? (currentPage / totalPages) * 100 : 0) + '%';
            }

            prevBtn.onclick = () => flipbook.turn('previous');
            nextBtn.onclick = () => flipbook.turn('next');

            document.addEventListener('keydown', e => {
                if (e.key === 'ArrowLeft') prevBtn.click();
                if (e.key === 'ArrowRight') nextBtn.click();
            });

             // --- Bookmarks ---
            toggleBookmarksBtn.addEventListener('click', () => bookmarksPanel.style.transform = 'translateX(0)');
            closeBookmarksBtn.addEventListener('click', () => bookmarksPanel.style.transform = 'translateX(100%)');

            async function loadBookmarks() {
                try {
                    const response = await fetch("{{ route('bookmarks.index', $book) }}");
                    const bookmarks = await response.json();
                    renderBookmarks(bookmarks);
                } catch (error) {
                    console.error("Bookmark loading error:", error);
                }
            }

            function renderBookmarks(bookmarks) {
                bookmarksList.innerHTML = '';
                if (bookmarks.length === 0) {
                    bookmarksList.innerHTML = '<div class="list-group-item text-muted">{{ __('Aucun marque-page.') }}</div>';
                    return;
                }
                bookmarks.forEach(bm => {
                    const pageNum = parseInt(bm.page_number, 10);
                    if(isNaN(pageNum)) return;

                    const item = document.createElement('div');
                    item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                    item.innerHTML = `
                        <div class="flex-grow-1" style="cursor: pointer;">
                            <div class="fw-bold">${bm.title}</div>
                            <small class="text-muted">Page ${pageNum}</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-primary edit-bookmark-btn" title="{{ __('Modifier') }}"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-outline-danger delete-bookmark-btn" title="{{ __('Supprimer') }}"><i class="bi bi-trash"></i></button>
                        </div>`;
                    
                    item.querySelector('.flex-grow-1').addEventListener('click', () => {
                        flipbook.turn('page', pageNum);
                        bookmarksPanel.style.transform = 'translateX(100%)';
                    });
                    
                    item.querySelector('.edit-bookmark-btn').addEventListener('click', (e) => { e.stopPropagation(); openBookmarkModal(bm); });
                    item.querySelector('.delete-bookmark-btn').addEventListener('click', (e) => { e.stopPropagation(); deleteBookmark(bm.id); });

                    bookmarksList.appendChild(item);
                });
            }

             function openBookmarkModal(bookmark = null) {
                bookmarkForm.reset();
                if (bookmark) {
                    bookmarkModalTitle.textContent = 'Modifier le marque-page';
                    bookmarkIdInput.value = bookmark.id;
                    bookmarkTitleInput.value = bookmark.title;
                    bookmarkPageNumSpan.textContent = bookmark.page_number;
                } else {
                    bookmarkModalTitle.textContent = 'Ajouter un marque-page';
                    bookmarkIdInput.value = '';
                    bookmarkPageNumSpan.textContent = flipbook.turn('page');
                }
                bookmarkModal.show();
            }

            addBookmarkBtn.addEventListener('click', () => openBookmarkModal());

            bookmarkForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const id = bookmarkIdInput.value;
                const title = bookmarkTitleInput.value;
                const pageNum = id ? document.getElementById('bookmark-page-number').textContent : flipbook.turn('page');
                const isEditing = !!id;

                const url = isEditing ? `/bookmarks/${id}` : "{{ route('bookmarks.store', $book) }}";
                const method = isEditing ? 'PUT' : 'POST';

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                        body: JSON.stringify({ title: title, page_number: pageNum })
                    });
                    if (!response.ok) throw new Error('Save failed');
                    bookmarkModal.hide();
                    loadBookmarks();
                } catch (error) {
                    console.error("Bookmark save error:", error);
                }
            });

            async function deleteBookmark(id) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer ce marque-page ?')) return;
                try {
                    const response = await fetch(`/bookmarks/${id}`, {
                        method: 'DELETE',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
                    });
                    if (!response.ok) throw new Error('Delete failed');
                    loadBookmarks();
                } catch (error) {
                    console.error("Bookmark delete error:", error);
                }
            }

            // --- Progress Saving ---
            function sendProgressUpdate() {
                const timeSpent = Math.round((Date.now() - startTime) / 1000);
                if (totalPages === 0) return;
                
                let currentPageForProgress = flipbook.turn('page');

                fetch("{{ route('read.progress', $book) }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    body: JSON.stringify({
                        total_pages: totalPages,
                        current_page: currentPageForProgress,
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
@endpush
