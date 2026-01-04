<!-- resources/views/admin/quizzes/create.blade.php -->

@extends('@extends('layouts.dashboard')outs.dashboard')')

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

        <button type="submit" class="btn btn-primary">{{ __('Generate Quiz') }}</button>
    </form>
</div>
@endsection
