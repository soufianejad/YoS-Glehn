<!-- resources/views/student/book/show.blade.php -->

@extends('layouts.student')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default_book_cover.png') }}" class="img-fluid" alt="{{ $book->title }}">
        </div>
        <div class="col-md-8">
            <h1>{{ $book->title }}</h1>
            <p><strong>{{ __('Author:') }}</strong> {{ $book->author->name }}</p>
            <p><strong>{{ __('Category:') }}</strong> <a href="{{ route('student.library.index', ['category' => $book->category->slug]) }}">{{ $book->category->name }}</a></p>
            <p><strong>{{ __('Description:') }}</strong> {{ $book->description }}</p>
            <p><strong>{{ __('Published Year:') }}</strong> {{ $book->published_year }}</p>
            <p><strong>{{ __('Language:') }}</strong> {{ $book->language }}</p>
            <p><strong>{{ __('ISBN:') }}</strong> {{ $book->isbn }}</p>

            @auth
                @if($book->readingProgress->isNotEmpty() && $book->readingProgress->first()->progress_percentage > 0 && $book->readingProgress->first()->progress_percentage < 100)
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $book->readingProgress->first()->progress_percentage }}%;" aria-valuenow="{{ $book->readingProgress->first()->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="card-text"><small class="text-muted">{{ __('Progress:') }} {{ number_format($book->readingProgress->first()->progress_percentage, 0) }}%</small></p>
                    <a href="{{ route('read.book', $book->slug) }}" class="btn btn-sm btn-success mt-2">{{ __('Continue Reading') }}</a>
                @elseif($book->readingProgress->isNotEmpty() && $book->readingProgress->first()->progress_percentage == 100)
                    <p class="card-text"><small class="text-success">{{ __('Completed!') }}</small></p>
                    <a href="{{ route('read.book', $book->slug) }}" class="btn btn-sm btn-info mt-2">{{ __('Read Again') }}</a>
                @endif
            @endauth

            @if($book->pdf_file)
                <a href="{{ route('read.book', $book->slug) }}" class="btn btn-primary">{{ __('Read Book') }}</a>
            @endif
            @if($book->audio_file)
                <a href="{{ route('student.book.listen', $book->slug) }}" class="btn btn-info">{{ __('Listen Audio') }}</a>
            @endif

            @if($book->quizzes->isNotEmpty())
                <a href="{{ route('student.quiz.book-quiz', $book->slug) }}" class="btn btn-warning">{{ __('Take the Quiz') }}</a>
            @endif

            @if($book->is_ai_quiz_enabled && auth()->check() && auth()->user()->isStudent())
                <form action="{{ route('book.generate_ai_quiz', $book) }}" method="POST" class="d-inline" id="ai-quiz-form">
                    @csrf
                    <button type="button" id="generate-ai-quiz-btn" class="btn btn-success" onclick="generateAiQuiz()">
                        <i class="fas fa-robot"></i> {{ __('Générer un Quiz (IA)') }}
                    </button>
                </form>

                <div id="ai-quiz-progress-container" class="mt-3" style="display: none;">
                    <p class="mb-1 text-muted small">{{ __('Génération du quiz en cours, veuillez patienter...') }}</p>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <div id="ai-quiz-message-container" class="mt-2" style="display: none;"></div>
            @endif

            <hr>

            @auth
            <div class="card my-4">
                <h5 class="card-header">{{ __('Leave a Review') }}</h5>
                <div class="card-body">
                    <form action="{{ route('review.store', $book) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="rating" class="form-label">{{ __('Rating') }}</label>
                            <select name="rating" id="rating" class="form-select" required>
                                <option value="5">{{ __('5 Stars') }}</option>
                                <option value="4">{{ __('4 Stars') }}</option>
                                <option value="3">{{ __('3 Stars') }}</option>
                                <option value="2">{{ __('2 Stars') }}</option>
                                <option value="1">{{ __('1 Star') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">{{ __('Comment') }}</label>
                            <textarea name="comment" id="comment" rows="3" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Submit Review') }}</button>
                    </form>
                </div>
            </div>
            @endauth

            <h3>{{ __('Reviews') }}</h3>
            @forelse($book->approvedReviews as $review)
                <div class="card mb-2">
                    <div class="card-body">
                        <h5 class="card-title">{{ $review->user->name }} - {{ __('Rating:') }} {{ $review->rating }}/5</h5>
                        <p class="card-text">{{ $review->comment }}</p>
                    </div>
                </div>
            @empty
                <p>{{ __('No reviews yet.') }}</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function generateAiQuiz() {
        if (!confirm('{{ __("Générer un quiz avec l\'IA basé sur ce livre ? Cela prendra quelques instants.") }}')) {
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
        messageContainer.className = 'mt-2 alert';
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
