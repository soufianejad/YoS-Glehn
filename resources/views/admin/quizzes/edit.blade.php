<!-- resources/views/admin/quizzes/edit.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Edit Quiz: ') . $quiz->title }}</h1>

    <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">{{ __('Quiz Title') }}</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $quiz->title) }}" required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="book_id" class="form-label">{{ __('Book') }}</label>
            <select class="form-control @error('book_id') is-invalid @enderror" id="book_id" name="book_id" required>
                <option value="">{{ __('Select Book') }}</option>
                @foreach($books as $book)
                    <option value="{{ $book->id }}" {{ old('book_id', $quiz->book_id) == $book->id ? 'selected' : '' }}>{{ $book->title }}</option>
                @endforeach
            </select>
            @error('book_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">{{ __('Description') }}</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $quiz->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="questions_count" class="form-label">{{ __('Number of Questions') }}</label>
            <input type="number" class="form-control @error('questions_count') is-invalid @enderror" id="questions_count" name="questions_count" value="{{ old('questions_count', $quiz->questions_count) }}" min="1" required>
            @error('questions_count')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="pass_score" class="form-label">{{ __('Pass Score (%)') }}</label>
            <input type="number" class="form-control @error('pass_score') is-invalid @enderror" id="pass_score" name="pass_score" value="{{ old('pass_score', $quiz->pass_score) }}" min="0" max="100" required>
            @error('pass_score')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="time_limit" class="form-label">{{ __('Time Limit (minutes)') }}</label>
            <input type="number" class="form-control @error('time_limit') is-invalid @enderror" id="time_limit" name="time_limit" value="{{ old('time_limit', $quiz->time_limit) }}" min="0" required>
            @error('time_limit')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $quiz->is_active) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">{{ __('Is Active') }}</label>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="show_correct_answers" name="show_correct_answers" value="1" {{ old('show_correct_answers', $quiz->show_correct_answers) ? 'checked' : '' }}>
            <label class="form-check-label" for="show_correct_answers">{{ __('Show Correct Answers') }}</label>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="randomize_questions" name="randomize_questions" value="1" {{ old('randomize_questions', $quiz->randomize_questions) ? 'checked' : '' }}>
            <label class="form-check-label" for="randomize_questions">{{ __('Randomize Questions') }}</label>
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Update Quiz') }}</button>
    </form>
</div>
@endsection
