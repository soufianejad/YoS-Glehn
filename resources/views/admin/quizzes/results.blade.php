<!-- resources/views/admin/quizzes/results.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Quiz Results for: ') . $quiz->title }}</h1>

    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Attempt ID') }}</th>
                <th>{{ __('User') }}</th>
                <th>{{ __('Score') }}</th>
                <th>{{ __('Percentage') }}</th>
                <th>{{ __('Passed') }}</th>
                <th>{{ __('Completed At') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attempts as $attempt)
                <tr>
                    <td>{{ $attempt->id }}</td>
                    <td>{{ $attempt->user->name ?? __('N/A') }}</td>
                    <td>{{ $attempt->correct_answers }} / {{ $attempt->total_questions }}</td>
                    <td>{{ number_format($attempt->percentage, 2) }}%</td>
                    <td>{{ $attempt->is_passed ? __('Yes') : __('No') }}</td>
                    <td>{{ $attempt->completed_at->format('M d, Y H:i') }}</td>
                    <td>
                        {{-- Add actions to view individual attempt details if needed --}}
                        <a href="#" class="btn btn-sm btn-info">{{ __('View Details') }}</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $attempts->links('pagination::bootstrap-5') }}

    <a href="{{ route('admin.quiz.index') }}" class="btn btn-secondary mt-3">{{ __('Back to Quizzes') }}</a>
</div>
@endsection
