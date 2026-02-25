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

        <!-- Titre -->
        <div class="row mb-3">
            <div class="col-12">
                <h1 class="h3 fw-bold text-primary mb-0">{{ $book->title }}</h1>
                @if ($book->author)
                    <p class="text-muted small mb-0">par {{ $book->author->name }}</p>
                @endif
            </div>
        </div>

        <!-- Lecteur -->
        <div class="row">
            <div class="col-12">
                @if ($book->pdf_file)
                    <div id="loading-indicator" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">{{ __('Chargement du livre...') }}</p>
                    </div>

                    <!-- Scène livre -->
                    <div id="book-stage" style="display:none;">
                        <div id="book-scene">
                            <!-- Page de fond (nouvelle page) -->
                            <canvas id="canvas-back"></canvas>
                            <!-- Page de devant (page actuelle qui part) -->
                            <canvas id="canvas-front"></canvas>
                            <!-- Feuille qui tourne (3D) -->
                            <div id="page-flipper">
                                <div id="flipper-front"><canvas id="canvas-flip-front"></canvas></div>
                                <div id="flipper-back"><canvas id="canvas-flip-back"></canvas></div>
                            </div>
                            <!-- Ombre portée de la reliure -->
                            <div id="spine-shadow"></div>
                            <!-- Zones nav -->
                            <div id="hit-prev" class="hit-zone hit-left"><span class="nav-arrow">&#8249;</span></div>
                            <div id="hit-next" class="hit-zone hit-right"><span class="nav-arrow">&#8250;</span></div>
                        </div>

                        <!-- Barre de contrôle -->
                        <div id="pdf-controls" class="mt-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 bg-light p-3 rounded shadow-sm">
                            <div class="d-flex gap-2">
                                <button id="prev" class="btn btn-outline-secondary btn-sm" disabled>
                                    <i class="bi bi-chevron-left"></i> {{ __('Précédent') }}
                                </button>
                                <button id="next" class="btn btn-outline-secondary btn-sm" disabled>
                                    {{ __('Suivant') }} <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                            <div class="text-center flex-grow-1">
                                <div class="fw-bold small">
                                    Page <span id="page-num">1</span> / <span id="page-count">0</span>
                                </div>
                                <div class="progress mt-1" style="height:5px;width:180px;margin:0 auto;">
                                    <div id="progress-bar" class="progress-bar bg-primary" style="width:0%;transition:width .4s ease;"></div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 flex-wrap justify-content-center">
                                <button id="toggle-bookmarks" class="btn btn-outline-info btn-sm"><i class="bi bi-bookmarks"></i></button>
                                <button id="add-bookmark"     class="btn btn-outline-success btn-sm"><i class="bi bi-bookmark-plus"></i></button>
                                <button id="zoom-out"         class="btn btn-outline-dark btn-sm"><i class="bi bi-zoom-out"></i></button>
                                <button id="zoom-in"          class="btn btn-outline-dark btn-sm"><i class="bi bi-zoom-in"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- Panneau marque-pages -->
                    <div id="bookmarks-panel" class="border-start bg-light p-3"
                        style="position:fixed;top:0;right:0;width:300px;height:100%;z-index:1050;transform:translateX(100%);transition:transform .3s ease;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">{{ __('Marque-pages') }}</h5>
                            <button id="close-bookmarks" class="btn-close"></button>
                        </div>
                        <div id="bookmarks-list" class="list-group"></div>
                    </div>
                @else
                    <div class="alert alert-warning text-center">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ __('Aucun fichier PDF disponible.') }}
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
                            <label class="form-label">{{ __('Titre') }}</label>
                            <input type="text" class="form-control" id="bookmark-title" required>
                        </div>
                        <p class="text-muted small">Page : <span id="bookmark-page-number"></span></p>
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
/* ── Scène ──────────────────────────────────────────── */
#book-stage {
    display: flex;
    flex-direction: column;
    align-items: center;
}

#book-scene {
    position: relative;
    display: inline-block;
    max-width: 100%;
    border-radius: 3px 6px 6px 3px;
    box-shadow:
        -4px 0 8px rgba(0,0,0,.12),
        0 8px 40px rgba(0,0,0,.30),
        0 2px 6px rgba(0,0,0,.15);
    background: #e8ddd0;
    overflow: hidden;
    /* perspective pour l'enfant 3D */
    perspective: 2000px;
    perspective-origin: center center;
    /* cursor page */
    cursor: default;
}

/* ── Canvas ──────────────────────────────────────────── */
#canvas-back,
#canvas-front {
    display: block;
    max-width: 100%;
    height: auto;
}
#canvas-back {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
}
#canvas-front {
    position: relative;
    z-index: 2;
}

/* ── Feuille 3D ──────────────────────────────────────── */
#page-flipper {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    z-index: 10;
    transform-style: preserve-3d;
    transform-origin: left center;
    pointer-events: none;
    /* caché par défaut */
    visibility: hidden;
}

#flipper-front,
#flipper-back {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
    overflow: hidden;
}

#flipper-front canvas,
#flipper-back canvas {
    display: block;
    width: 100%;
    height: 100%;
}

/* Face arrière = miroir horizontal */
#flipper-back {
    transform: rotateY(180deg);
}

/* ── Gradient de pli (appliqué via pseudo sur flipper-front) ─ */
#flipper-front::after {
    content: '';
    position: absolute;
    top: 0; right: 0;
    width: 40px; height: 100%;
    background: linear-gradient(to right,
        transparent 0%,
        rgba(0,0,0,.04) 60%,
        rgba(0,0,0,.18) 100%
    );
    pointer-events: none;
    z-index: 3;
}

/* Lumière sur la face arrière au retournement */
#flipper-back::after {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 40px; height: 100%;
    background: linear-gradient(to left,
        transparent 0%,
        rgba(0,0,0,.04) 60%,
        rgba(0,0,0,.18) 100%
    );
    pointer-events: none;
    z-index: 3;
}

/* ── Ombre au sol pendant le flip ────────────────────── */
#flipper-front::before {
    content: '';
    position: absolute;
    top: 0; right: -20px;
    width: 30px; height: 100%;
    background: linear-gradient(to right,
        rgba(0,0,0,.22) 0%,
        transparent 100%
    );
    z-index: 4;
    pointer-events: none;
    opacity: 0;
    transition: opacity .1s;
}

/* ── Ombre reliure ──────────────────────────────────── */
#spine-shadow {
    position: absolute;
    top: 0; left: 0;
    width: 16px; height: 100%;
    background: linear-gradient(to right,
        rgba(0,0,0,.22) 0%,
        rgba(0,0,0,.06) 60%,
        transparent 100%
    );
    z-index: 20;
    pointer-events: none;
    border-radius: 3px 0 0 3px;
}

/* ── Animations ──────────────────────────────────────── */
@keyframes flipForward {
    0%   { transform: rotateY(0deg); }
    100% { transform: rotateY(-180deg); }
}
@keyframes flipBackward {
    0%   { transform: rotateY(0deg); }
    100% { transform: rotateY(180deg); }
}

/* ── Zones de navigation ─────────────────────────────── */
.hit-zone {
    position: absolute;
    top: 0;
    height: 100%;
    width: 18%;
    z-index: 30;
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: background .25s;
}
.hit-left  {
    left: 0;
    justify-content: flex-start;
    padding-left: 10px;
    background: linear-gradient(to right, rgba(255,255,255,.08), transparent);
}
.hit-right {
    right: 0;
    justify-content: flex-end;
    padding-right: 10px;
    background: linear-gradient(to left, rgba(255,255,255,.08), transparent);
}
.hit-zone:hover { background: rgba(255,255,255,.18); }
.hit-zone.disabled { opacity: 0; pointer-events: none; }

.nav-arrow {
    font-size: clamp(1.8rem, 5vw, 3rem);
    color: rgba(0,0,0,0);
    transition: color .2s, transform .2s;
    font-weight: 300;
    line-height: 1;
    text-shadow: 0 1px 4px rgba(0,0,0,.25);
    user-select: none;
}
.hit-zone:hover .nav-arrow { color: rgba(50,50,50,.6); }
.hit-left:hover  .nav-arrow { transform: translateX(-3px); }
.hit-right:hover .nav-arrow { transform: translateX(3px); }

/* ── Responsive ──────────────────────────────────────── */
@media (max-width: 576px) {
    .hit-zone { width: 22%; }
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

    /* ── Refs ──────────────────────────────────────────── */
    const loadingEl      = document.getElementById('loading-indicator');
    const bookStage      = document.getElementById('book-stage');
    const bookScene      = document.getElementById('book-scene');
    const canvasBack     = document.getElementById('canvas-back');
    const canvasFront    = document.getElementById('canvas-front');
    const flipper        = document.getElementById('page-flipper');
    const flipFront      = document.getElementById('canvas-flip-front');
    const flipBack       = document.getElementById('canvas-flip-back');
    const controls       = document.getElementById('pdf-controls');
    const pageNumSpan    = document.getElementById('page-num');
    const pageCountSpan  = document.getElementById('page-count');
    const progressBar    = document.getElementById('progress-bar');
    const prevBtn        = document.getElementById('prev');
    const nextBtn        = document.getElementById('next');
    const zoomInBtn      = document.getElementById('zoom-in');
    const zoomOutBtn     = document.getElementById('zoom-out');
    const hitPrev        = document.getElementById('hit-prev');
    const hitNext        = document.getElementById('hit-next');

    /* Marque-pages */
    const toggleBmBtn    = document.getElementById('toggle-bookmarks');
    const closeBmBtn     = document.getElementById('close-bookmarks');
    const bmPanel        = document.getElementById('bookmarks-panel');
    const bmList         = document.getElementById('bookmarks-list');
    const addBmBtn       = document.getElementById('add-bookmark');
    const bmModal        = new bootstrap.Modal(document.getElementById('bookmark-modal'));
    const bmForm         = document.getElementById('bookmark-form');
    const bmModalTitle   = document.getElementById('bookmark-modal-title');
    const bmIdInput      = document.getElementById('bookmark-id');
    const bmTitleInput   = document.getElementById('bookmark-title');
    const bmPageSpan     = document.getElementById('bookmark-page-number');

    /* ── État ──────────────────────────────────────────── */
    let pdfDoc      = null;
    let totalPages  = 0;
    let currentPage = {{ $initialPage > 1 ? $initialPage : 1 }};
    let scale       = 1.5;
    let isAnimating = false;
    let startTime   = Date.now();
    const DURATION  = 550; // ms durée du flip

    const pdfUrl = "{{ route('read.pdf.content', $book) }}?_token={{ $token }}";

    /* ── Chargement PDF ────────────────────────────────── */
    fetch(pdfUrl, { headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        .then(r => { if (!r.ok) throw new Error(); return r.arrayBuffer(); })
        .then(d  => pdfjsLib.getDocument({ data: d }).promise)
        .then(async pdf => {
            pdfDoc     = pdf;
            totalPages = pdf.numPages;
            pageCountSpan.textContent = totalPages;
            await renderToCanvas(canvasFront, currentPage);
            /* dimensionner back au même format */
            canvasBack.width  = canvasFront.width;
            canvasBack.height = canvasFront.height;
            loadingEl.style.display = 'none';
            bookStage.style.display = 'flex';
            updateUI();
            loadBookmarks();
            sendProgressUpdate();
        })
        .catch(() => {
            loadingEl.innerHTML = `<div class="alert alert-danger">{{ __('Erreur de chargement du PDF.') }}</div>`;
        });

    /* ── Rendu sur un canvas ───────────────────────────── */
    async function renderToCanvas(canvas, num) {
        const page     = await pdfDoc.getPage(num);
        const vp       = page.getViewport({ scale });
        canvas.width   = vp.width;
        canvas.height  = vp.height;
        await page.render({ canvasContext: canvas.getContext('2d'), viewport: vp }).promise;
    }

    /* ── Copier un canvas dans un autre ────────────────── */
    function copyCanvas(src, dst) {
        dst.width  = src.width;
        dst.height = src.height;
        dst.getContext('2d').drawImage(src, 0, 0);
    }

    /* ── Animation de flip ─────────────────────────────── */
    async function goToPage(target, dir) {
        if (isAnimating || !pdfDoc) return;
        if (target < 1 || target > totalPages) return;
        isAnimating = true;

        /* 1. Préparer les trois couches :
              canvas-back  = page cible (visible derrière)
              flipFront    = page courante (face avant de la feuille qui part)
              flipBack     = page cible miroir (face arrière, révélée à mi-chemin) */
        await renderToCanvas(canvasBack, target);
        copyCanvas(canvasFront, flipFront);
        copyCanvas(canvasBack,  flipBack);

        /* 2. Positionner le flipper selon la direction */
        if (dir === 'next') {
            /* tourne de gauche → droite (sort vers la droite) */
            flipper.style.transformOrigin = 'left center';
        } else {
            /* tourne de droite → gauche */
            flipper.style.transformOrigin = 'right center';
        }

        /* Reset avant animation */
        flipper.style.transition = 'none';
        flipper.style.transform  = dir === 'next' ? 'rotateY(0deg)' : 'rotateY(0deg)';
        flipper.style.visibility = 'visible';

        /* 3. Forcer reflow puis lancer */
        flipper.getBoundingClientRect();
        flipper.style.transition = `transform ${DURATION}ms cubic-bezier(0.645, 0.045, 0.355, 1.000)`;
        flipper.style.transform  = dir === 'next' ? 'rotateY(-180deg)' : 'rotateY(180deg)';

        /* 4. Fin d'animation */
        setTimeout(async () => {
            /* Afficher la nouvelle page sur le canvas front */
            copyCanvas(canvasBack, canvasFront);
            flipper.style.visibility = 'hidden';
            flipper.style.transition = 'none';
            flipper.style.transform  = 'rotateY(0deg)';

            currentPage = target;
            updateUI();
            sendProgressUpdate();
            isAnimating = false;
        }, DURATION);
    }

    /* ── UI ────────────────────────────────────────────── */
    function updateUI() {
        pageNumSpan.textContent = currentPage;
        progressBar.style.width = (currentPage / totalPages * 100) + '%';
        prevBtn.disabled = currentPage <= 1;
        nextBtn.disabled = currentPage >= totalPages;
        hitPrev.classList.toggle('disabled', currentPage <= 1);
        hitNext.classList.toggle('disabled', currentPage >= totalPages);
    }

    /* ── Zoom ──────────────────────────────────────────── */
    async function applyZoom() {
        await renderToCanvas(canvasFront, currentPage);
        canvasBack.width  = canvasFront.width;
        canvasBack.height = canvasFront.height;
    }
    zoomInBtn.onclick  = () => { if (scale < 3)   { scale += 0.25; applyZoom(); } };
    zoomOutBtn.onclick = () => { if (scale > 0.5) { scale -= 0.25; applyZoom(); } };

    /* ── Navigation ────────────────────────────────────── */
    prevBtn.onclick = () => goToPage(currentPage - 1, 'prev');
    nextBtn.onclick = () => goToPage(currentPage + 1, 'next');
    hitPrev.onclick = () => goToPage(currentPage - 1, 'prev');
    hitNext.onclick = () => goToPage(currentPage + 1, 'next');

    document.addEventListener('keydown', e => {
        if (e.key === 'ArrowLeft')  goToPage(currentPage - 1, 'prev');
        if (e.key === 'ArrowRight') goToPage(currentPage + 1, 'next');
    });

    /* Swipe */
    let tx = 0;
    bookScene.addEventListener('touchstart', e => { tx = e.changedTouches[0].clientX; }, { passive: true });
    bookScene.addEventListener('touchend',   e => {
        const dx = e.changedTouches[0].clientX - tx;
        if (Math.abs(dx) > 40) dx < 0
            ? goToPage(currentPage + 1, 'next')
            : goToPage(currentPage - 1, 'prev');
    });

    /* ── Marque-pages ──────────────────────────────────── */
    toggleBmBtn.addEventListener('click', () => bmPanel.style.transform = 'translateX(0)');
    closeBmBtn.addEventListener('click',  () => bmPanel.style.transform = 'translateX(100%)');

    async function loadBookmarks() {
        try {
            const r = await fetch("{{ route('bookmarks.index', $book) }}");
            if (!r.ok) throw new Error();
            renderBookmarks(await r.json());
        } catch {
            bmList.innerHTML = '<div class="list-group-item text-danger">{{ __('Erreur de chargement.') }}</div>';
        }
    }

    function renderBookmarks(list) {
        bmList.innerHTML = '';
        if (!list.length) {
            bmList.innerHTML = '<div class="list-group-item text-muted">{{ __('Aucun marque-page.') }}</div>';
            return;
        }
        list.forEach(bm => {
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
                const target = parseInt(bm.page_number);
                goToPage(target, target >= currentPage ? 'next' : 'prev');
                bmPanel.style.transform = 'translateX(100%)';
            });
            item.querySelector('.edit-bm').addEventListener('click', () => openBmModal(bm));
            item.querySelector('.del-bm').addEventListener('click',  () => deleteBm(bm.id));
            bmList.appendChild(item);
        });
    }

    function openBmModal(bm = null) {
        bmForm.reset();
        if (bm) {
            bmModalTitle.textContent = '{{ __('Modifier le marque-page') }}';
            bmIdInput.value    = bm.id;
            bmTitleInput.value = bm.title;
            bmPageSpan.textContent = bm.page_number;
        } else {
            bmModalTitle.textContent = '{{ __('Ajouter un marque-page') }}';
            bmIdInput.value = '';
            bmPageSpan.textContent = currentPage;
        }
        bmModal.show();
    }

    addBmBtn.addEventListener('click', () => openBmModal());

    bmForm.addEventListener('submit', async e => {
        e.preventDefault();
        const id  = bmIdInput.value;
        const url = id ? `/bookmarks/${id}` : "{{ route('bookmarks.store', $book) }}";
        try {
            const r = await fetch(url, {
                method:  id ? 'PUT' : 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body:    JSON.stringify({ title: bmTitleInput.value, page_number: currentPage })
            });
            if (!r.ok) throw new Error();
            bmModal.hide();
            loadBookmarks();
        } catch { alert('{{ __('Erreur lors de l\'enregistrement.') }}'); }
    });

    async function deleteBm(id) {
        if (!confirm('{{ __('Supprimer ce marque-page ?') }}')) return;
        try {
            const r = await fetch(`/bookmarks/${id}`, {
                method:  'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            if (!r.ok) throw new Error();
            loadBookmarks();
        } catch { alert('{{ __('Erreur lors de la suppression.') }}'); }
    }

    /* ── Progression ───────────────────────────────────── */
    function sendProgressUpdate() {
        if (!totalPages) return;
        const timeSpent = Math.round((Date.now() - startTime) / 1000);
        fetch("{{ route('read.progress', $book) }}", {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body:    JSON.stringify({ total_pages: totalPages, current_page: currentPage, time_spent: timeSpent })
        }).finally(() => { startTime = Date.now(); });
    }

    const interval = setInterval(sendProgressUpdate, 30000);
    window.addEventListener('beforeunload', sendProgressUpdate);
    window.addEventListener('unload', () => clearInterval(interval));
});
</script>
@endpush