<!-- resources/views/admin/quizzes/index.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Quiz Management') }}</h1>



    <div class="table-responsive">
<table class="table">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Book') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quizzes as $quiz)
                <tr>
                    <td>{{ $quiz->id }}</td>
                    <td>{{ $quiz->title }}</td>
                    <td>{{ $quiz->book->title ?? __('N/A') }}</td>
                    <td>{{ $quiz->status }}</td>
                    <td>
                        <a href="{{ route('admin.quiz.show', $quiz) }}" class="btn btn-sm btn-info">{{ __('View') }}</a>
                        <a href="{{ route('admin.quiz.edit', $quiz) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                        <form action="{{ route('admin.quiz.destroy', $quiz) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __("Are you sure?") }}')">{{ __('Delete') }}</button>
                        </form>
                        <form action="{{ route('admin.quiz.regenerate', $quiz) }}" method="POST" class="d-inline regenerate-quiz-form">
                            @csrf
                            <button type="button" class="btn btn-sm btn-secondary regenerate-btn" onclick="regenerateQuiz(this)">{{ __('Regenerate') }}</button>
                        </form>
                        <a href="{{ route('admin.quiz.results', $quiz) }}" class="btn btn-sm btn-primary">{{ __('Results') }}</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

    {{ $quizzes->links('pagination::bootstrap-5') }}
</div>

<!-- Progress Bar and Messages Container Modal -->
<div class="modal fade" id="regenerateQuizModal" tabindex="-1" aria-labelledby="regenerateQuizModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="regenerateQuizModalLabel">{{ __('Régénération du Quiz') }}</h5>
            </div>
            <div class="modal-body">
                <div id="regenerate-quiz-message-container" style="display: none;"></div>

                <div id="regenerate-quiz-progress-container">
                    <p class="mb-1 text-center font-weight-bold" id="regenerate-quiz-progress-text">{{ __('Régénération en cours, veuillez patienter...') }}</p>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-secondary" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="regenerate-quiz-modal-footer" style="display: none;">
                <button type="button" class="btn btn-primary" onclick="window.location.reload()">{{ __('Fermer et recharger') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    function regenerateQuiz(btnElement) {
        if (!confirm('{{ __("Are you sure you want to regenerate this quiz?") }}')) {
            return;
        }

        const form = btnElement.closest('form');
        const modalElement = document.getElementById('regenerateQuizModal');
        // Check if Bootstrap JS is available (for modal)
        let modal;
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            modal = new bootstrap.Modal(modalElement);
        } else if (window.jQuery && window.jQuery.fn.modal) {
             modal = {
                 show: () => $(modalElement).modal('show'),
                 hide: () => $(modalElement).modal('hide')
             }
        }

        const progressContainer = document.getElementById('regenerate-quiz-progress-container');
        const messageContainer = document.getElementById('regenerate-quiz-message-container');
        const footer = document.getElementById('regenerate-quiz-modal-footer');

        // Show modal and progress
        if (modal) modal.show();
        progressContainer.style.display = 'block';
        messageContainer.style.display = 'none';
        footer.style.display = 'none';
        messageContainer.innerHTML = '';
        messageContainer.className = 'alert';

        // Disable all regenerate buttons
        document.querySelectorAll('.regenerate-btn').forEach(b => b.disabled = true);

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
            footer.style.display = 'block';

            if (data.success) {
                messageContainer.classList.add('alert-success');
                messageContainer.innerHTML = '<strong><i class="fas fa-check-circle"></i> {{ __("Succès!") }}</strong> ' + data.message;
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                messageContainer.classList.add('alert-danger');
                messageContainer.innerHTML = '<strong><i class="fas fa-exclamation-triangle"></i> {{ __("Erreur") }}</strong><br>' + (data.message || '{{ __("Une erreur inconnue s\'est produite.") }}');
                document.querySelectorAll('.regenerate-btn').forEach(b => b.disabled = false);
            }
        })
        .catch(error => {
            progressContainer.style.display = 'none';
            messageContainer.style.display = 'block';
            footer.style.display = 'block';
            messageContainer.classList.add('alert-danger');

            let errorMsg = '{{ __("Une erreur de communication avec le serveur s\'est produite.") }}';
            if (error && error.message) {
                errorMsg = error.message;
            } else if (error && error.errors) {
                 const firstError = Object.values(error.errors)[0];
                 errorMsg = Array.isArray(firstError) ? firstError[0] : firstError;
            }

            messageContainer.innerHTML = '<strong><i class="fas fa-exclamation-triangle"></i> {{ __("Erreur") }}</strong><br>' + errorMsg;
            document.querySelectorAll('.regenerate-btn').forEach(b => b.disabled = false);
        });
    }
</script>
@endsection
