@extends('layouts.dashboard')

@section('title', isset($quiz) ? 'Modifier le Quiz' : 'Créer un Quiz')
@section('header', isset($quiz) ? 'Modifier le Quiz : ' . $quiz->title : 'Nouveau Quiz pour : ' . $book->title)

@section('content')
<div class="container-fluid">
    <form action="{{ isset($quiz) ? (Auth::user()->isAdmin() ? route('admin.quiz.update', $quiz) : route('teacher.quizzes.update', $quiz)) : (Auth::user()->isAdmin() ? route('admin.quiz.store') : route('teacher.quizzes.store')) }}" method="POST" id="quiz-form">
        @csrf
        @if(isset($quiz))
            @method('PUT')
        @endif
        <input type="hidden" name="book_id" value="{{ $book->id }}">

        <div class="row">
            <!-- Left Column: Quiz Details -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4" style="position: sticky; top: 1rem;">
                    <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Détails du Quiz</h6></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre du Quiz*</label>
                            <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $quiz->title ?? 'Quiz pour ' . $book->title) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $quiz->description ?? '') }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pass_score" class="form-label">Score pour réussir (%)*</label>
                                <input type="number" id="pass_score" name="pass_score" class="form-control" value="{{ old('pass_score', $quiz->pass_score ?? 50) }}" min="0" max="100" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="time_limit" class="form-label">Temps imparti (minutes)</label>
                                <input type="number" id="time_limit" name="time_limit" class="form-control" value="{{ old('time_limit', $quiz->time_limit ?? '') }}" min="1">
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @if(old('is_active', $quiz->is_active ?? true)) checked @endif>
                            <label class="form-check-label" for="is_active">Activer le quiz immédiatement</label>
                        </div>
                    </div>
                     <div class="card-footer text-end">
                        <a href="{{ Auth::user()->isAdmin() ? route('admin.quiz.index') : route('teacher.quizzes.select-book') }}" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">{{ isset($quiz) ? 'Mettre à jour' : 'Créer le Quiz' }}</button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Questions -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Questions</h6>
                        <button type="button" id="add-question" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-1"></i> Ajouter une question
                        </button>
                    </div>
                    <div class="card-body" id="questions-container">
                        @if(isset($quiz) && $quiz->questions->isNotEmpty())
                            @foreach($quiz->questions->sortBy('order') as $question)
                                <div class="border rounded p-3 mb-3 question-block bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="font-weight-bold question-title">Question {{ $loop->iteration }}</h6>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary move-up" title="Monter"><i class="fas fa-arrow-up"></i></button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary move-down" title="Descendre"><i class="fas fa-arrow-down"></i></button>
                                            <button type="button" class="btn btn-sm btn-danger remove-question" title="Supprimer"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                    <input type="hidden" class="question-id" name="questions[{{ $question->id }}][id]" value="{{ $question->id }}">
                                    <input type="hidden" class="question-order" name="questions[{{ $question->id }}][order]" value="{{ $question->order }}">
                                    <div class="mb-3">
                                        <label for="question_text_{{ $question->id }}" class="form-label">Texte de la question*</label>
                                        <input type="text" id="question_text_{{ $question->id }}" name="questions[{{ $question->id }}][question_text]" class="form-control" value="{{ $question->question_text }}" required>
                                    </div>
                                    <div class="row">
                                        @foreach($question->options as $j => $option)
                                            <div class="col-md-6 mb-2">
                                                <div class="input-group">
                                                    <div class="input-group-text">
                                                        <input class="form-check-input mt-0" type="radio" name="questions[{{ $question->id }}][correct_answer]" value="{{ $j }}" required @if($question->correct_answer == $j) checked @endif>
                                                    </div>
                                                    <input type="text" name="questions[{{ $question->id }}][options][{{ $j }}]" class="form-control" placeholder="Option {{ $j + 1 }}" value="{{ $option }}" required>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <label for="points_{{ $question->id }}" class="form-label">Points*</label>
                                            <input type="number" id="points_{{ $question->id }}" name="questions[{{ $question->id }}][points]" class="form-control" value="{{ $question->points }}" min="1" required>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="explanation_{{ $question->id }}" class="form-label">Explication (optionnel)</label>
                                            <input type="text" id="explanation_{{ $question->id }}" name="questions[{{ $question->id }}][explanation]" value="{{ $question->explanation }}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<template id="question-template">
    <div class="border rounded p-3 mb-3 question-block bg-light">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="font-weight-bold question-title">Question</h6>
            <div>
                <button type="button" class="btn btn-sm btn-outline-secondary move-up" title="Monter"><i class="fas fa-arrow-up"></i></button>
                <button type="button" class="btn btn-sm btn-outline-secondary move-down" title="Descendre"><i class="fas fa-arrow-down"></i></button>
                <button type="button" class="btn btn-sm btn-danger remove-question" title="Supprimer"><i class="fas fa-trash"></i></button>
            </div>
        </div>
        <input type="hidden" class="question-id" name="questions[NEW___INDEX__][id]" value="">
        <input type="hidden" class="question-order" name="questions[NEW___INDEX__][order]" value="">
        <div class="mb-3">
            <label for="question_text_NEW___INDEX__" class="form-label">Texte de la question*</label>
            <input type="text" id="question_text_NEW___INDEX__" name="questions[NEW___INDEX__][question_text]" class="form-control" required>
        </div>
        <div class="row">
            @for ($j = 0; $j < 4; $j++)
                <div class="col-md-6 mb-2">
                    <div class="input-group">
                        <div class="input-group-text">
                            <input class="form-check-input mt-0" type="radio" name="questions[NEW___INDEX__][correct_answer]" value="{{ $j }}" required>
                        </div>
                        <input type="text" name="questions[NEW___INDEX__][options][{{ $j }}]" class="form-control" placeholder="Option {{ $j + 1 }}" required>
                    </div>
                </div>
            @endfor
        </div>
        <div class="row mt-2">
            <div class="col-md-4">
                 <label for="points_NEW___INDEX__" class="form-label">Points*</label>
                <input type="number" id="points_NEW___INDEX__" name="questions[NEW___INDEX__][points]" class="form-control" value="10" min="1" required>
            </div>
            <div class="col-md-8">
                <label for="explanation_NEW___INDEX__" class="form-label">Explication (optionnel)</label>
                <input type="text" id="explanation_NEW___INDEX__" name="questions[NEW___INDEX__][explanation]" class="form-control">
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('questions-container');
    const template = document.getElementById('question-template');
    const addBtn = document.getElementById('add-question');
    let questionIndex = {{ isset($quiz) ? $quiz->questions->max('id') + 1 : 0 }}; // Start index high to avoid collisions

    function addQuestion() {
        const index = 'new_' + questionIndex++;
        const clone = template.content.cloneNode(true);
        
        // Update names and IDs
        let content = new XMLSerializer().serializeToString(clone);
        content = content.replace(/__INDEX__/g, index);
        const newFragment = document.createRange().createContextualFragment(content);
        
        container.appendChild(newFragment);
        const newBlock = container.lastElementChild;
        attachEventListeners(newBlock);
        updateQuestionNumbers();
    }

    function removeQuestion(e) {
        e.target.closest('.question-block').remove();
        updateQuestionNumbers();
    }

    function moveQuestion(e, direction) {
        const questionBlock = e.target.closest('.question-block');
        if (direction === 'up' && questionBlock.previousElementSibling) {
            container.insertBefore(questionBlock, questionBlock.previousElementSibling);
        } else if (direction === 'down' && questionBlock.nextElementSibling) {
            container.insertBefore(questionBlock.nextElementSibling, questionBlock);
        }
        updateQuestionNumbers();
    }
    
    function attachEventListeners(block) {
        block.querySelector('.remove-question').addEventListener('click', removeQuestion);
        block.querySelector('.move-up').addEventListener('click', (e) => moveQuestion(e, 'up'));
        block.querySelector('.move-down').addEventListener('click', (e) => moveQuestion(e, 'down'));
    }

    function updateQuestionNumbers() {
        const blocks = container.querySelectorAll('.question-block');
        blocks.forEach((block, index) => {
            block.querySelector('.question-title').textContent = `Question ${index + 1}`;
            block.querySelector('.question-order').value = index + 1;
        });
    }

    // Attach listeners to existing questions on edit page
    container.querySelectorAll('.question-block').forEach(attachEventListeners);

    addBtn.addEventListener('click', addQuestion);

    // If it's a create form with no old input, add one question to start with
    @if(!isset($quiz) && !old('questions'))
        addQuestion();
    @endif
});
</script>
@endpush

@section('title', 'Créer un Quiz')
@section('header', 'Nouveau Quiz pour : ' . $book->title)

@section('content')
<div class="container-fluid">
    <form action="{{ Auth::user()->isAdmin() ? route('admin.quiz.store') : route('teacher.quizzes.store') }}" method="POST" id="quiz-form">
        @csrf
        <input type="hidden" name="book_id" value="{{ $book->id }}">

        <div class="row">
            <!-- Left Column: Quiz Details -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4" style="position: sticky; top: 1rem;">
                    <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">Détails du Quiz</h6></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre du Quiz*</label>
                            <input type="text" id="title" name="title" class="form-control" value="{{ old('title', 'Quiz pour ' . $book->title) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="pass_score" class="form-label">Score pour réussir (%)*</label>
                                <input type="number" id="pass_score" name="pass_score" class="form-control" value="{{ old('pass_score', 50) }}" min="0" max="100" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="time_limit" class="form-label">Temps imparti (minutes)</label>
                                <input type="number" id="time_limit" name="time_limit" class="form-control" value="{{ old('time_limit') }}" min="1">
                            </div>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Activer le quiz immédiatement</label>
                        </div>
                    </div>
                     <div class="card-footer text-end">
                        <a href="{{ Auth::user()->isAdmin() ? route('admin.books.index') : route('teacher.quizzes.select-book') }}" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">Créer le Quiz</button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Questions -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Questions</h6>
                        <button type="button" id="add-question" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-1"></i> Ajouter une question
                        </button>
                    </div>
                    <div class="card-body" id="questions-container">
                        {{-- Questions will be added here dynamically --}}
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<template id="question-template">
    <div class="border rounded p-3 mb-3 question-block bg-light">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="font-weight-bold question-title">Question</h6>
            <div>
                <button type="button" class="btn btn-sm btn-outline-secondary move-up" title="Monter">
                    <i class="fas fa-arrow-up"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary move-down" title="Descendre">
                    <i class="fas fa-arrow-down"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger remove-question" title="Supprimer">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <input type="hidden" class="question-order" name="questions[__INDEX__][order]" value="">
        <div class="mb-3">
            <label for="question_text___INDEX__" class="form-label">Texte de la question*</label>
            <input type="text" id="question_text___INDEX__" name="questions[__INDEX__][question_text]" class="form-control" required>
        </div>
        <div class="row">
            @for ($j = 0; $j < 4; $j++)
                <div class="col-md-6 mb-2">
                    <div class="input-group">
                        <div class="input-group-text">
                            <input class="form-check-input mt-0" type="radio" name="questions[__INDEX__][correct_answer]" value="{{ $j }}" required>
                        </div>
                        <input type="text" name="questions[__INDEX__][options][{{ $j }}]" class="form-control" placeholder="Option {{ $j + 1 }}" required>
                    </div>
                </div>
            @endfor
        </div>
        <div class="row mt-2">
            <div class="col-md-4">
                 <label for="points___INDEX__" class="form-label">Points*</label>
                <input type="number" id="points___INDEX__" name="questions[__INDEX__][points]" class="form-control" value="10" min="1" required>
            </div>
            <div class="col-md-8">
                <label for="explanation___INDEX__" class="form-label">Explication (optionnel)</label>
                <input type="text" id="explanation___INDEX__" name="questions[__INDEX__][explanation]" class="form-control">
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('questions-container');
    const template = document.getElementById('question-template');
    const addBtn = document.getElementById('add-question');
    let questionIndex = 0;

    function addQuestion() {
        const index = questionIndex++;
        const clone = template.content.cloneNode(true);
        const questionBlock = clone.querySelector('.question-block');

        // Update names and IDs
        questionBlock.innerHTML = questionBlock.innerHTML.replace(/__INDEX__/g, index);
        
        container.appendChild(clone);
        attachEventListeners(questionBlock);
        updateQuestionNumbers();
    }

    function removeQuestion(e) {
        e.target.closest('.question-block').remove();
        updateQuestionNumbers();
    }

    function moveQuestion(e, direction) {
        const questionBlock = e.target.closest('.question-block');
        if (direction === 'up' && questionBlock.previousElementSibling) {
            container.insertBefore(questionBlock, questionBlock.previousElementSibling);
        } else if (direction === 'down' && questionBlock.nextElementSibling) {
            container.insertBefore(questionBlock.nextElementSibling, questionBlock);
        }
        updateQuestionNumbers();
    }
    
    function attachEventListeners(block) {
        block.querySelector('.remove-question').addEventListener('click', removeQuestion);
        block.querySelector('.move-up').addEventListener('click', (e) => moveQuestion(e, 'up'));
        block.querySelector('.move-down').addEventListener('click', (e) => moveQuestion(e, 'down'));
    }

    function updateQuestionNumbers() {
        const blocks = container.querySelectorAll('.question-block');
        blocks.forEach((block, index) => {
            block.querySelector('.question-title').textContent = `Question ${index + 1}`;
            block.querySelector('.question-order').value = index + 1;
        });
    }

    addBtn.addEventListener('click', addQuestion);

    // Add one question to start with
    addQuestion();
});
</script>
@endpush
