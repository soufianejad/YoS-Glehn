<!-- resources/views/admin/books/show.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Book Details: ') . $book->title }}</h1>

    <div class="card">
        <div class="card-header">{{ __('Book Information') }}</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default_book_cover.png') }}" class="img-fluid" alt="{{ $book->title }}">
                </div>
                <div class="col-md-8">
                    <p><strong>{{ __('ID:') }}</strong> {{ $book->id }}</p>
                    <p><strong>{{ __('Title:') }}</strong> {{ $book->title }}</p>
                    <p><strong>{{ __('Slug:') }}</strong> {{ $book->slug }}</p>
                    <p><strong>{{ __('Description:') }}</strong> {{ $book->description }}</p>
                    <p><strong>{{ __('Author:') }}</strong> {{ $book->author->name ?? __('N/A') }}</p>
                    <p><strong>{{ __('Category:') }}</strong> {{ $book->category->name ?? __('N/A') }}</p>
                    <p><strong>{{ __('PDF File:') }}</strong> @if($book->pdf_file) <a href="{{ asset('storage/' . $book->pdf_file) }}" target="_blank">{{ __('View PDF') }}</a> @else {{ __('N/A') }} @endif</p>
                    <p><strong>{{ __('Audio File:') }}</strong> @if($book->audio_file) <a href="{{ asset('storage/' . $book->audio_file) }}" target="_blank">{{ __('Listen Audio') }}</a> @else {{ __('N/A') }} @endif</p>
                    <p><strong>{{ __('PDF Pages:') }}</strong> {{ $book->pdf_pages ?? __('N/A') }}</p>
                    <p><strong>{{ __('Audio Duration:') }}</strong> {{ $book->audio_duration ?? __('N/A') }} {{ __('seconds') }}</p>
                    <p><strong>{{ __('ISBN:') }}</strong> {{ $book->isbn ?? __('N/A') }}</p>
                    <p><strong>{{ __('Published Year:') }}</strong> {{ $book->published_year ?? __('N/A') }}</p>
                    <p><strong>{{ __('Language:') }}</strong> {{ $book->language ?? __('N/A') }}</p>
                    <p><strong>{{ __('Space:') }}</strong> {{ $book->space }}</p>
                    <p><strong>{{ __('Content Type:') }}</strong> {{ $book->content_type }}</p>
                    <p><strong>{{ __('PDF Price:') }}</strong> {{ formatPrice($book->pdf_price ?? 0) }}</p>
                    <p><strong>{{ __('Audio Price:') }}</strong> {{ formatPrice($book->audio_price ?? 0) }}</p>
                    <p><strong>{{ __('Status:') }}</strong> {{ $book->status }}</p>
                    <p><strong>{{ __('Created At:') }}</strong> {{ $book->created_at->format('M d, Y H:i') }}</p>
                    <p><strong>{{ __('Last Updated:') }}</strong> {{ $book->updated_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-warning mt-3">{{ __('Edit Book') }}</a>
    <a href="{{ route('admin.books.index') }}" class="btn btn-secondary mt-3">{{ __('Back to Books') }}</a>

    @if($book->is_ai_quiz_enabled)
    <form action="{{ route('admin.books.generate_ai_quiz', $book) }}" method="POST" class="d-inline" id="ai-quiz-form">
        @csrf
        <button type="button" id="generate-ai-quiz-btn" class="btn btn-success mt-3" onclick="generateAiQuiz()">
            {{ __('Générer un Quiz global (IA)') }}
        </button>
    </form>

    <div id="ai-quiz-progress-container" class="mt-3" style="display: none; max-width: 400px;">
        <p class="mb-1 text-muted small">{{ __('Génération du quiz en cours, veuillez patienter...') }}</p>
        <div class="progress" style="height: 10px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

    <div id="ai-quiz-message-container" class="mt-3" style="display: none; max-width: 400px;"></div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function generateAiQuiz() {
        if (!confirm('{{ __("Êtes-vous sûr de vouloir générer un quiz global avec l\'IA pour ce livre ? Cela peut prendre quelques secondes.") }}')) {
            return;
        }

        const form = document.getElementById('ai-quiz-form');
        const btn = document.getElementById('generate-ai-quiz-btn');
        const progressContainer = document.getElementById('ai-quiz-progress-container');
        const messageContainer = document.getElementById('ai-quiz-message-container');

        // Reset state
        btn.disabled = true;
        progressContainer.style.display = 'block';
        messageContainer.style.display = 'none';
        messageContainer.className = 'mt-3 alert';
        messageContainer.innerHTML = '';

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(result => {
            progressContainer.style.display = 'none';
            btn.disabled = false;

            messageContainer.style.display = 'block';
            if (result.status >= 200 && result.status < 300 && result.body.success) {
                messageContainer.classList.add('alert-success');
                messageContainer.innerHTML = '<i class="fas fa-check-circle"></i> ' + (result.body.message || '{{ __("Quiz généré avec succès !") }}');
            } else {
                messageContainer.classList.add('alert-danger');
                messageContainer.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + (result.body.message || '{{ __("Une erreur est survenue.") }}');
            }
        })
        .catch(error => {
            progressContainer.style.display = 'none';
            btn.disabled = false;

            messageContainer.style.display = 'block';
            messageContainer.classList.add('alert-danger');
            messageContainer.innerHTML = '<i class="fas fa-exclamation-circle"></i> {{ __("Une erreur réseau est survenue.") }}';
            console.error('Error:', error);
        });
    }
</script>
@endpush
