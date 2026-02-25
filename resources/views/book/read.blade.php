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
                            <i class="{{ auth()->user()->favorites->contains($book->id) ? 'fas fa-heart' : 'far fa-heart' }} me-1"></i>
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
                            <span class="visually-hidden">{{ __('Chargement du PDF...') }}</span>
                        </div>
                        <p class="mt-2 text-muted">{{ __('Chargement du livre...') }}</p>
                    </div>

                    <!-- Conteneur du flipbook (une seule page visible) -->
                    <div id="flipbook-wrapper" style="display:none;">
                        <div id="book-scene">
                            <div id="book-page-container">
                                <!-- Page actuelle -->
                                <canvas id="canvas-current"></canvas>
                                <!-- Page suivante/précédente (pour l'animation) -->
                                <canvas id="canvas-next" style="display:none;"></canvas>
                                <!-- Overlay de l'effet de tournage -->
                                <div id="page-turn-overlay"></div>
                            </div>
                            <!-- Zones cliquables gauche/droite -->
                            <div id="hit-prev" class="hit-zone hit-left" title="{{ __('Page précédente') }}">
                                <i class="bi bi-chevron-left"></i>
                            </div>
                            <div id="hit-next" class="hit-zone hit-right" title="{{ __('Page suivante') }}">
                                <i class="bi bi-chevron-right"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Barre de contrôle -->
                    <div id="pdf-controls"
                        class="mt-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 bg-light p-3 rounded shadow-sm"
                        style="display: none !important;">
                        <!-- Navigation -->
                        <div class="d-flex gap-2">
                            <button id="prev" class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="bi bi-chevron-left"></i> {{ __('Précédent') }}
                            </button>
                            <button id="next" class="btn btn-outline-secondary btn-sm" disabled>
                                {{ __('Suivant') }} <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>

                        <!-- Infos page + progression -->
                        <div class="text-center flex-grow-1">
                            <div class="fw-bold">
                                Page <span id="page-num">1</span> / <span id="page-count">0</span>
                            </div>
                            <div class="progress mt-2" style="height: 6px; width: 200px; margin: 0 auto;">
                                <div id="progress-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>

                        <!-- Actions supplémentaires -->
                        <div class="d-flex gap-2 flex-wrap justify-content-center">
                            <button id="toggle-bookmarks" class="btn btn-outline-info btn-sm" title="{{ __('Marque-pages') }}">
                                <i class="bi bi-bookmarks"></i>
                            </button>
                            <button id="add-bookmark" class="btn btn-outline-success btn-sm" title="{{ __('Ajouter un marque-page') }}">
                                <i class="bi bi-bookmark-plus"></i>
                            </button>
                            <button id="zoom-out" class="btn btn-outline-dark btn-sm" title="{{ __('Zoom -') }}">
                                <i class="bi bi-zoom-out"></i>
                            </button>
                            <button id="zoom-in" class="btn btn-outline-dark btn-sm" title="{{ __('Zoom +') }}">
                                <i class="bi bi-zoom-in"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Panneau des marque-pages -->
                    <div id="bookmarks-panel" class="border-start bg-light p-3"
                        style="position: fixed; top: 0; right: 0; width: 300px; height: 100%; z-index: 1050; transform: translateX(100%); transition: transform 0.3s ease-in-out;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ __('Marque-pages') }}</h5>
                            <button id="close-bookmarks" class="btn-close"></button>
                        </div>
                        <div id="bookmarks-list" class="list-group"></div>
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

    <!-- Modal marque-page -->
    <div class="modal fade" id="bookmark-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookmark-modal-title">{{ __('Ajouter un marque-page') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
/* ── Scène du livre ─────────────────────────────── */
#book-scene {
    position: relative;
    width: 100%;
    max-width: 700px;
    margin: 0 auto;
    background: #f0e9df;
    border-radius: 4px;
    box-shadow:
        0 2px 6px rgba(0,0,0,.15),
        4px 4px 20px rgba(0,0,0,.25),
        inset -3px 0 8px rgba(0,0,0,.08);
    overflow: hidden;
    /* hauteur dynamique via JS */
}

#book-page-container {
    position: relative;
    width: 100%;
    height: 100%;
}

#canvas-current,
#canvas-next {
    display: block;
    width: 100%;
    height: auto;
    position: absolute;
    top: 0;
    left: 0;
}

/* ── Effet page qui tourne (CSS 3D) ─────────────── */
#page-turn-overlay {
    position: absolute;
    top: 0;
    right: 0;
    width: 0;
    height: 100%;
    pointer-events: none;
    transform-origin: left center;
    perspective: 1200px;
    z-index: 10;
}

/* Animation vers la droite (page suivante) */
@keyframes turnForward {
    0%   { transform: rotateY(0deg);   opacity: 1; }
    50%  { transform: rotateY(-90deg); opacity: 0.6; }
    100% { transform: rotateY(-180deg);opacity: 0; }
}

/* Animation vers la gauche (page précédente) */
@keyframes turnBackward {
    0%   { transform: rotateY(0deg);   opacity: 1; }
    50%  { transform: rotateY(90deg);  opacity: 0.6; }
    100% { transform: rotateY(180deg); opacity: 0; }
}

.turning-forward #page-turn-overlay {
    width: 100%;
    animation: turnForward 0.45s ease-in-out forwards;
}

.turning-backward #page-turn-overlay {
    width: 100%;
    animation: turnBackward 0.45s ease-in-out forwards;
}

/* Ombre de reliure */
#book-scene::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 8px;
    height: 100%;
    background: linear-gradient(to right, rgba(0,0,0,.18), transparent);
    z-index: 5;
    pointer-events: none;
}

/* ── Zones cliquables gauche / droite ───────────── */
.hit-zone {
    position: absolute;
    top: 0;
    height: 100%;
    width: 15%;
    z-index: 20;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: rgba(0,0,0,.0);
    font-size: 2rem;
    transition: color .2s, background .2s;
}
.hit-zone:hover {
    color: rgba(0,0,0,.45);
    background: rgba(255,255,255,.25);
}
.hit-left  { left: 0; }
.hit-right { right: 0; }

/* ── Responsive ─────────────────────────────────── */
@media (max-width: 576px) {
    .hit-zone { width: 20%; font-size: 1.4rem; }
    #book-scene { border-radius: 2px; }
}
</style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {

        /* ── DOM refs ─────────────────────────────────────── */
        const loadingEl       = document.getElementById('loading-indicator');
        const flipbookWrapper = document.getElementById('flipbook-wrapper');
        const bookScene       = document.getElementById('book-scene');
        const pageContainer   = document.getElementById('book-page-container');
        const canvasCurrent   = document.getElementById('canvas-current');
        const canvasNext      = document.getElementById('canvas-next');
        const controls        = document.getElementById('pdf-controls');
        const pageNumSpan     = document.getElementById('page-num');
        const pageCountSpan   = document.getElementById('page-count');
        const progressBar     = document.getElementById('progress-bar');
        const prevBtn         = document.getElementById('prev');
        const nextBtn         = document.getElementById('next');
        const zoomInBtn       = document.getElementById('zoom-in');
        const zoomOutBtn      = document.getElementById('zoom-out');
        const hitPrev         = document.getElementById('hit-prev');
        const hitNext         = document.getElementById('hit-next');

        /* Marque-pages */
        const toggleBookmarksBtn  = document.getElementById('toggle-bookmarks');
        const closeBookmarksBtn   = document.getElementById('close-bookmarks');
        const bookmarksPanel      = document.getElementById('bookmarks-panel');
        const bookmarksList       = document.getElementById('bookmarks-list');
        const addBookmarkBtn      = document.getElementById('add-bookmark');
        const bookmarkModal       = new bootstrap.Modal(document.getElementById('bookmark-modal'));
        const bookmarkForm        = document.getElementById('bookmark-form');
        const bookmarkModalTitle  = document.getElementById('bookmark-modal-title');
        const bookmarkIdInput     = document.getElementById('bookmark-id');
        const bookmarkTitleInput  = document.getElementById('bookmark-title');
        const bookmarkPageNumSpan = document.getElementById('bookmark-page-number');

        /* ── État ─────────────────────────────────────────── */
        let pdfDoc      = null;
        let totalPages  = 0;
        let currentPage = {{ $initialPage > 1 ? $initialPage : 1 }};
        let scale       = 1.5;
        let isAnimating = false;
        let startTime   = Date.now();

        const pdfUrl = "{{ route('read.pdf.content', $book) }}?_token={{ $token }}";

        /* ── Chargement du PDF ────────────────────────────── */
        fetch(pdfUrl, { headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(r => { if (!r.ok) throw new Error('PDF inaccessible'); return r.arrayBuffer(); })
            .then(data => pdfjsLib.getDocument({ data }).promise)
            .then(pdf => {
                pdfDoc     = pdf;
                totalPages = pdf.numPages;
                pageCountSpan.textContent = totalPages;
                return renderToCanvas(canvasCurrent, currentPage);
            })
            .then(() => {
                loadingEl.style.display = 'none';
                flipbookWrapper.style.display = 'block';
                controls.style.removeProperty('display');
                controls.style.display = 'flex';
                syncSceneHeight();
                updateUI();
                loadBookmarks();
                sendProgressUpdate();
            })
            .catch(err => {
                console.error(err);
                loadingEl.innerHTML = `<div class="alert alert-danger">{{ __('Erreur de chargement du PDF.') }}</div>`;
            });

        /* ── Rendu d'une page sur un canvas ──────────────── */
        function renderToCanvas(canvas, pageNum) {
            return pdfDoc.getPage(pageNum).then(page => {
                const vp  = page.getViewport({ scale });
                canvas.width  = vp.width;
                canvas.height = vp.height;
                return page.render({ canvasContext: canvas.getContext('2d'), viewport: vp }).promise;
            });
        }

        /* Ajuste la hauteur de la scène au canvas courant */
        function syncSceneHeight() {
            bookScene.style.height = canvasCurrent.height
                ? canvasCurrent.height + 'px'
                : 'auto';
        }

        /* ── Navigation avec animation ───────────────────── */
        function goToPage(targetPage, direction) {
            if (isAnimating || !pdfDoc) return;
            if (targetPage < 1 || targetPage > totalPages) return;

            isAnimating = true;

            // Rendre la page cible dans le canvas "next" (invisible)
            canvasNext.style.display = 'none';
            renderToCanvas(canvasNext, targetPage).then(() => {

                // Copier le canvas courant dans l'overlay pour l'animation
                const overlay = document.getElementById('page-turn-overlay');
                const overlayCanvas = document.createElement('canvas');
                overlayCanvas.width  = canvasCurrent.width;
                overlayCanvas.height = canvasCurrent.height;
                overlayCanvas.style.cssText = 'width:100%;height:100%;display:block;';
                overlayCanvas.getContext('2d').drawImage(canvasCurrent, 0, 0);
                overlay.innerHTML = '';
                overlay.appendChild(overlayCanvas);

                // Afficher la nouvelle page derrière
                canvasCurrent.getContext('2d').drawImage(canvasNext, 0, 0);

                // Lancer l'animation CSS
                const cls = direction === 'next' ? 'turning-forward' : 'turning-backward';
                bookScene.classList.add(cls);

                bookScene.addEventListener('animationend', () => {
                    bookScene.classList.remove(cls);
                    overlay.innerHTML = '';
                    currentPage = targetPage;
                    updateUI();
                    sendProgressUpdate();
                    isAnimating = false;
                }, { once: true });
            });
        }

        /* ── Mise à jour UI ──────────────────────────────── */
        function updateUI() {
            pageNumSpan.textContent = currentPage;
            progressBar.style.width = (currentPage / totalPages * 100) + '%';
            prevBtn.disabled = currentPage <= 1;
            nextBtn.disabled = currentPage >= totalPages;
            hitPrev.style.opacity = currentPage <= 1 ? '0' : '1';
            hitNext.style.opacity = currentPage >= totalPages ? '0' : '1';
            syncSceneHeight();
        }

        /* ── Zoom ────────────────────────────────────────── */
        function applyZoom() {
            renderToCanvas(canvasCurrent, currentPage).then(() => {
                syncSceneHeight();
            });
        }

        zoomInBtn.onclick  = () => { if (scale < 3)   { scale += 0.25; applyZoom(); } };
        zoomOutBtn.onclick = () => { if (scale > 0.5) { scale -= 0.25; applyZoom(); } };

        /* ── Boutons & clavier ───────────────────────────── */
        prevBtn.onclick = () => goToPage(currentPage - 1, 'prev');
        nextBtn.onclick = () => goToPage(currentPage + 1, 'next');
        hitPrev.onclick = () => goToPage(currentPage - 1, 'prev');
        hitNext.onclick = () => goToPage(currentPage + 1, 'next');

        document.addEventListener('keydown', e => {
            if (e.key === 'ArrowLeft')  goToPage(currentPage - 1, 'prev');
            if (e.key === 'ArrowRight') goToPage(currentPage + 1, 'next');
        });

        /* Swipe tactile */
        let touchStartX = 0;
        bookScene.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].clientX; }, { passive: true });
        bookScene.addEventListener('touchend',   e => {
            const dx = e.changedTouches[0].clientX - touchStartX;
            if (Math.abs(dx) > 40) {
                dx < 0 ? goToPage(currentPage + 1, 'next') : goToPage(currentPage - 1, 'prev');
            }
        });

        /* ── Responsive : recalcul au resize ────────────── */
        window.addEventListener('resize', () => syncSceneHeight());

        /* ── Marque-pages ────────────────────────────────── */
        toggleBookmarksBtn.addEventListener('click', () => bookmarksPanel.style.transform = 'translateX(0)');
        closeBookmarksBtn.addEventListener('click',  () => bookmarksPanel.style.transform = 'translateX(100%)');

        async function loadBookmarks() {
            try {
                const r = await fetch("{{ route('bookmarks.index', $book) }}");
                if (!r.ok) throw new Error();
                renderBookmarks(await r.json());
            } catch { bookmarksList.innerHTML = '<div class="list-group-item text-danger">{{ __('Erreur de chargement.') }}</div>'; }
        }

        function renderBookmarks(bookmarks) {
            bookmarksList.innerHTML = '';
            if (!bookmarks.length) {
                bookmarksList.innerHTML = '<div class="list-group-item text-muted">{{ __('Aucun marque-page.') }}</div>';
                return;
            }
            bookmarks.forEach(bm => {
                const item = document.createElement('div');
                item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                item.innerHTML = `
                    <div class="flex-grow-1" style="cursor:pointer">
                        <div class="fw-bold">${bm.title}</div>
                        <small class="text-muted">Page ${bm.page_number}</small>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-primary edit-bm"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger del-bm"><i class="bi bi-trash"></i></button>
                    </div>`;
                item.querySelector('.flex-grow-1').addEventListener('click', () => {
                    goToPage(parseInt(bm.page_number), currentPage < bm.page_number ? 'next' : 'prev');
                    bookmarksPanel.style.transform = 'translateX(100%)';
                });
                item.querySelector('.edit-bm').addEventListener('click', () => openBookmarkModal(bm));
                item.querySelector('.del-bm').addEventListener('click',  () => deleteBookmark(bm.id));
                bookmarksList.appendChild(item);
            });
        }

        function openBookmarkModal(bookmark = null) {
            bookmarkForm.reset();
            if (bookmark) {
                bookmarkModalTitle.textContent = '{{ __('Modifier le marque-page') }}';
                bookmarkIdInput.value          = bookmark.id;
                bookmarkTitleInput.value       = bookmark.title;
                bookmarkPageNumSpan.textContent = bookmark.page_number;
            } else {
                bookmarkModalTitle.textContent  = '{{ __('Ajouter un marque-page') }}';
                bookmarkIdInput.value           = '';
                bookmarkPageNumSpan.textContent = currentPage;
            }
            bookmarkModal.show();
        }

        addBookmarkBtn.addEventListener('click', () => openBookmarkModal());

        bookmarkForm.addEventListener('submit', async e => {
            e.preventDefault();
            const id      = bookmarkIdInput.value;
            const url     = id ? `/bookmarks/${id}` : "{{ route('bookmarks.store', $book) }}";
            const method  = id ? 'PUT' : 'POST';
            try {
                const r = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ title: bookmarkTitleInput.value, page_number: currentPage })
                });
                if (!r.ok) throw new Error();
                bookmarkModal.hide();
                loadBookmarks();
            } catch { alert('{{ __('Erreur lors de l\'enregistrement.') }}'); }
        });

        async function deleteBookmark(id) {
            if (!confirm('{{ __('Êtes-vous sûr de vouloir supprimer ce marque-page ?') }}')) return;
            try {
                const r = await fetch(`/bookmarks/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                if (!r.ok) throw new Error();
                loadBookmarks();
            } catch { alert('{{ __('Erreur lors de la suppression.') }}'); }
        }

        /* ── Progression ─────────────────────────────────── */
        function sendProgressUpdate() {
            if (!totalPages) return;
            const timeSpent = Math.round((Date.now() - startTime) / 1000);
            fetch("{{ route('read.progress', $book) }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ total_pages: totalPages, current_page: currentPage, time_spent: timeSpent })
            }).finally(() => { startTime = Date.now(); });
        }

        const interval = setInterval(sendProgressUpdate, 30000);
        window.addEventListener('beforeunload', sendProgressUpdate);
        window.addEventListener('unload', () => clearInterval(interval));
    });
    </script>
@endpush