<!-- resources/views/admin/quizzes/show.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Quiz Details: ') . $quiz->title }}</h1>

    <div class="card mb-4">
        <div class="card-header">{{ __('Quiz Information') }}</div>
        <div class="card-body">
            <p><strong>{{ __('Book:') }}</strong> {{ $quiz->book->title ?? __('N/A') }}</p>
            <p><strong>{{ __('Description:') }}</strong> {{ $quiz->description }}</p>
            <p><strong>{{ __('Number of Questions:') }}</strong> {{ $quiz->questions_count }}</p>
            <p><strong>{{ __('Pass Score:') }}</strong> {{ $quiz->pass_score }}%</p>
            <p><strong>{{ __('Time Limit:') }}</strong> {{ $quiz->time_limit }} minutes</p>
            <p><strong>{{ __('Status:') }}</strong> {{ $quiz->is_active ? __('Active') : __('Inactive') }}</p>
            <p><strong>{{ __('Show Correct Answers:') }}</strong> {{ $quiz->show_correct_answers ? __('Yes') : __('No') }}</p>
            <p><strong>{{ __('Randomize Questions:') }}</strong> {{ $quiz->randomize_questions ? __('Yes') : __('No') }}</p>
        </div>
    </div>

    <h2>{{ __('Questions') }}</h2>
    @forelse($quiz->questions as $question)
        <div class="card mb-3">
            <div class="card-header">{{ __('Question ') . $loop->iteration }}</div>
            <div class="card-body">
                <p><strong>{{ __('Question Text:') }}</strong> {{ $question->question_text }}</p>
                <p><strong>{{ __('Type:') }}</strong> {{ $question->question_type }}</p>
                <p><strong>{{ __('Options:') }}</strong></p>
                <ol type="A">
                    @foreach($question->options as $option)
                        <li>{{ $option }}</li>
                    @endforeach
                </ol>
                <p><strong>{{ __('Correct Answer:') }}</strong> {{ $question->options[$question->correct_answer] ?? __('N/A') }}</p>
                <p><strong>{{ __('Explanation:') }}</strong> {{ $question->explanation ?? __('N/A') }}</p>
            </div>
        </div>
    @empty
        <p>{{ __('No questions for this quiz yet.') }}</p>
    @endforelse

    <a href="{{ route('admin.quiz.edit', $quiz) }}" class="btn btn-warning mt-3">{{ __('Edit Quiz') }}</a>
    <a href="{{ route('admin.quiz.index') }}" class="btn btn-secondary mt-3">{{ __('Back to Quizzes') }}</a>
</div>
@endsection
