<!-- resources/views/admin/quizzes/create.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Create New Quiz for Book: ') . $book->title }}</h1>

    <form action="{{ route('admin.quiz.generate', $book) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">{{ __('Quiz Title') }}</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="number_of_questions" class="form-label">{{ __('Number of Questions') }}</label>
            <input type="number" class="form-control @error('number_of_questions') is-invalid @enderror" id="number_of_questions" name="number_of_questions" value="{{ old('number_of_questions', 5) }}" min="1" required>
            @error('number_of_questions')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="difficulty" class="form-label">{{ __('Difficulty') }}</label>
            <select class="form-control @error('difficulty') is-invalid @enderror" id="difficulty" name="difficulty" required>
                <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>{{ __('Easy') }}</option>
                <option value="medium" {{ old('difficulty') == 'medium' ? 'selected' : '' }}>{{ __('Medium') }}</option>
                <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>{{ __('Hard') }}</option>
            </select>
            @error('difficulty')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="button" id="generate-ai-quiz-btn" class="btn btn-primary" onclick="generateAiQuiz()">{{ __('Generate Quiz') }}</button>
    </form>

    <!-- Progress Bar and Messages Container -->
    <div id="ai-quiz-message-container" class="mt-3" style="display: none;"></div>

    <div id="ai-quiz-progress-container" class="mt-3" style="display: none;">
        <p class="mb-1 text-center font-weight-bold" id="ai-quiz-progress-text">{{ __('Génération du quiz en cours, veuillez patienter...') }}</p>
        <div class="progress" style="height: 20px;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
</div>

<script>
    function generateAiQuiz() {
        const form = document.querySelector('form');
        const btn = document.getElementById('generate-ai-quiz-btn');
        const progressContainer = document.getElementById('ai-quiz-progress-container');
        const messageContainer = document.getElementById('ai-quiz-message-container');

        // Disable button and show progress
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ __("Génération...") }}';
        progressContainer.style.display = 'block';
        messageContainer.style.display = 'none';
        messageContainer.innerHTML = '';
        messageContainer.className = 'mt-3 alert';

        // Prepare form data
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            progressContainer.style.display = 'none';
            messageContainer.style.display = 'block';

            if (data.success) {
                messageContainer.classList.add('alert-success');
                messageContainer.innerHTML = '<strong><i class="fas fa-check-circle"></i> {{ __("Succès!") }}</strong> ' + data.message;
                // Redirect if provided
                if (data.redirect_url) {
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1500);
                }
            } else {
                messageContainer.classList.add('alert-danger');
                messageContainer.innerHTML = '<strong><i class="fas fa-exclamation-triangle"></i> {{ __("Erreur lors de la génération du quiz") }}</strong><br>' + (data.message || '{{ __("Une erreur inconnue s\'est produite.") }}');

                // Reset button
                btn.disabled = false;
                btn.innerHTML = '{{ __("Generate Quiz") }}';
            }
        })
        .catch(error => {
            progressContainer.style.display = 'none';
            messageContainer.style.display = 'block';
            messageContainer.classList.add('alert-danger');

            let errorMsg = '{{ __("Une erreur de communication avec le serveur s\'est produite.") }}';
            if (error && error.message) {
                errorMsg = error.message;
            } else if (error && error.errors) {
                 const firstError = Object.values(error.errors)[0];
                 errorMsg = Array.isArray(firstError) ? firstError[0] : firstError;
            }

            messageContainer.innerHTML = '<strong><i class="fas fa-exclamation-triangle"></i> {{ __("Erreur lors de la génération du quiz") }}</strong><br>' + errorMsg;

            // Reset button
            btn.disabled = false;
            btn.innerHTML = '{{ __("Generate Quiz") }}';
        });
    }
</script>
@endsection
