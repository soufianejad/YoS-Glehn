@extends('layouts.student')

@section('title', 'Quiz: ' . $quiz->title)

@push('styles')
<style>
    .quiz-header {
        position: sticky;
        top: 0;
        z-index: 1020;
        background-color: #fff;
        padding: 1rem;
        border-bottom: 1px solid #e3e6f0;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
    }
    .question-card {
        scroll-margin-top: 150px; /* Offset for sticky header */
    }
</style>
@endpush

@section('content')
<div class="quiz-header">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $quiz->title }}</h5>
            <div id="countdown-timer" class="font-weight-bold h4 text-danger mb-0"></div>
        </div>
        <div class="progress mt-2" style="height: 5px;">
            <div id="quiz-progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div id="questions-answered" class="text-muted small mt-1">0 / {{ $questions->count() }} questions répondues</div>
    </div>
</div>

<div class="container py-4">
    <form id="quiz-form" action="{{ route('student.quiz.submit', $quiz) }}" method="POST">
        @csrf
        <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">

        @foreach($questions as $question)
            <div class="card shadow-sm mb-4 question-card" id="question-{{ $loop->iteration }}">
                <div class="card-header d-flex justify-content-between">
                    <span>Question {{ $loop->iteration }}</span>
                    <span>{{ $question->points }} {{ Str::plural('point', $question->points) }}</span>
                </div>
                <div class="card-body">
                    <p class="lead">{{ $question->question_text }}</p>
                    <hr>
                    @if($question->question_type === 'multiple_choice')
                        <div class="options-group">
                        @foreach($question->getOptionsArrayAttribute() as $key => $option)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" id="question_{{ $question->id }}_option_{{ $key }}" value="{{ $key }}" required>
                                <label class="form-check-label" for="question_{{ $question->id }}_option_{{ $key }}">
                                    {{ $option }}
                                </label>
                            </div>
                        @endforeach
                        </div>
                    @endif
                    {{-- Add other question types here if needed --}}
                </div>
            </div>
        @endforeach

        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-check-circle me-2"></i> Soumettre mes réponses
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const timeLimitInMinutes = {{ $quiz->time_limit }};
    if (timeLimitInMinutes > 0) {
        const timerElement = document.getElementById('countdown-timer');
        const quizForm = document.getElementById('quiz-form');
        
        let timeRemaining = timeLimitInMinutes * 60;

        const timerInterval = setInterval(function() {
            timeRemaining--;

            let hours = Math.floor(timeRemaining / 3600);
            let minutes = Math.floor((timeRemaining % 3600) / 60);
            let seconds = timeRemaining % 60;

            // Add leading zero if needed
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            if (hours > 0) {
                timerElement.textContent = `${hours}:${minutes}:${seconds}`;
            } else {
                timerElement.textContent = `${minutes}:${seconds}`;
            }

            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                alert('Le temps est écoulé ! Votre quiz va être soumis automatiquement.');
                quizForm.submit();
            }
        }, 1000);
    }

    // Progress bar logic
    const progressBar = document.getElementById('quiz-progress-bar');
    const questionsAnsweredText = document.getElementById('questions-answered');
    const totalQuestions = {{ $questions->count() }};
    const radioGroups = document.querySelectorAll('.options-group');
    
    function updateProgress() {
        let answeredCount = 0;
        document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
             answeredCount++;
        });

        const percentage = totalQuestions > 0 ? (answeredCount / totalQuestions) * 100 : 0;
        progressBar.style.width = percentage + '%';
        progressBar.setAttribute('aria-valuenow', percentage);
        questionsAnsweredText.textContent = `${answeredCount} / ${totalQuestions} questions répondues`;
    }

    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', updateProgress);
    });

    updateProgress(); // Initial check
});
</script>
@endpush
