<!-- resources/views/student/book/read.blade.php -->

@extends('layouts.student')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>{{ $book->title }}</h1>
            <p class="lead">By {{ $book->author->name ?? 'N/A' }}</p>
            <p>{{ $book->description ?? 'No description available.' }}</p>
            <hr>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ __('Book Details') }}</div>
                <div class="card-body">
                    <p><strong>{{ __('Category:') }}</strong> {{ $book->category->name ?? 'N/A' }}</p>
                    <p><strong>{{ __('Level:') }}</strong> {{ $book->level ?? 'N/A' }}</p>
                    <p><strong>{{ __('Pages:') }}</strong> {{ $book->pdf_pages ?? 'N/A' }}</p>
                    <p><strong>{{ __('Published:') }}</strong> {{ $book->published_at ? $book->published_at->format('M d, Y') : 'N/A' }}</p>
                    <p><strong>{{ __('ISBN:') }}</strong> {{ $book->isbn ?? 'N/A' }}</p>
                    <p><strong>{{ __('Language:') }}</strong> {{ $book->language ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">{{ __('Actions') }}</div>
                <div class="card-body">
                    <a href="{{ route('student.book.show', $book) }}" class="btn btn-info btn-block">{{ __('Back to Book Details') }}</a>
                    @if($book->audio_file)
                        <a href="{{ route('student.book.listen', $book) }}" class="btn btn-primary btn-block mt-2">{{ __('Listen to Audio') }}</a>
                    @endif
                    @if($book->pdf_file)
                        <a href="{{ route('student.book.download', $book) }}" class="btn btn-success btn-block mt-2">{{ __('Download PDF') }}</a>
                    @endif
                    @if($book->has_quiz)
                        <a href="{{ route('student.quiz.book-quiz', $book) }}" class="btn btn-warning btn-block mt-2">{{ __('Take Quiz') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @if($book->pdf_file)
                <div id="pdf-container" style="border:1px solid #ccc; padding:10px; width: 100%; height: 80vh; overflow: auto;"></div>
                <div class="mt-2 d-flex justify-content-between align-items-center">
                    <div>
                        <button id="prev" class="btn btn-secondary">{{ __('Previous') }}</button>
                        <button id="next" class="btn btn-secondary">{{ __('Next') }}</button>
                    </div>
                    <span>Page: <span id="page-num">1</span> / <span id="page-count">0</span></span>
                </div>
            @else
                <p>{{ __('No PDF available for this book.') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection

<!-- PDF.js library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js"></script>

<script>
// Tell PDF.js where the worker is
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('pdf-container');
    const pageNumSpan = document.getElementById('page-num');
    const pageCountSpan = document.getElementById('page-count');
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');

    let pdfDoc = null;
    let currentPage = {{ $initialPage > 0 ? $initialPage : 1 }}; // Initialize with initialPage
    let totalPages = 0;
    let timeSpent = 0;
    let startTime = Date.now();
    let lastUpdateTime = Date.now();
    // Removed UPDATE_INTERVAL and updateTimeout

    // Load PDF
    pdfjsLib.getDocument("{{ asset('storage/' . $book->pdf_file) }}").promise.then(pdf => {
        pdfDoc = pdf;
        totalPages = pdf.numPages;
        pageCountSpan.textContent = totalPages;
        renderPage(currentPage); // Render initial page
    }).catch(error => {
        console.error('Error loading PDF:', error);
        container.innerHTML = '<p>{{ __('Error loading PDF. Please try again later.') }}</p>';
    });

    function renderPage(num) {
        if (!pdfDoc || num < 1 || num > totalPages) return;
        pdfDoc.getPage(num).then(page => {
            const viewport = page.getViewport({ scale: 1.5 });
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.width = viewport.width;
            canvas.height = viewport.height;

            container.innerHTML = '';
            container.appendChild(canvas);

            page.render({ canvasContext: context, viewport: viewport }).promise.then(() => {
                currentPage = num;
                pageNumSpan.textContent = currentPage;
                // Removed sendProgressUpdate() from here
            });
        }).catch(error => {
            console.error('Error rendering page:', error);
        });
    }

    // Navigation
    const prevPage = () => {
        if (currentPage > 1) {
            renderPage(currentPage - 1);
            sendProgressUpdate(); // Update progress on page change
        }
    };

    const nextPage = () => {
        if (currentPage < totalPages) {
            renderPage(currentPage + 1);
            sendProgressUpdate(); // Update progress on page change
        }
    };

    prevBtn.addEventListener('click', prevPage);
    nextBtn.addEventListener('click', nextPage);

    const handleKeydown = e => {
        if (e.key === 'ArrowLeft') prevPage();
        if (e.key === 'ArrowRight') nextPage();
    };

    document.addEventListener('keydown', handleKeydown);

    // Debounce function to limit fetch requests
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Send progress update
    function sendProgressUpdate() {
        timeSpent += (Date.now() - startTime) / 1000;
        // Only send update if current page is valid and time spent is significant
        if (currentPage > 0 && totalPages > 0 && timeSpent >= 1) {
            fetch("{{ route('student.book.reading-progress', $book) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    total_pages: totalPages,
                    current_page: currentPage,
                    time_spent: Math.round(timeSpent)
                })
            }).then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
            }).catch(error => {
                console.error('Error sending progress update:', error);
            }).finally(() => {
                startTime = Date.now();
                timeSpent = 0;
            });
        } else {
            startTime = Date.now(); // Reset start time even if not sending update
            timeSpent = 0;
        }
    }

    // Removed debouncedSendProgressUpdate and scheduleUpdate

    // Clean up on page unload
    window.addEventListener('beforeunload', () => {
        // clearTimeout(updateTimeout); // Removed
        sendProgressUpdate(); // Final update before leaving
    });

    // Clean up event listeners
    window.addEventListener('unload', () => {
        prevBtn.removeEventListener('click', prevPage);
        nextBtn.removeEventListener('click', nextPage);
        document.removeEventListener('keydown', handleKeydown);
    });
});
</script>