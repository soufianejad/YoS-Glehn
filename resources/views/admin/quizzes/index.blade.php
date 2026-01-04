<!-- resources/views/admin/quizzes/index.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Quiz Management') }}</h1>



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
                        <form action="{{ route('admin.quiz.regenerate', $quiz) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-secondary">{{ __('Regenerate') }}</button>
                        </form>
                        <a href="{{ route('admin.quiz.results', $quiz) }}" class="btn btn-sm btn-primary">{{ __('Results') }}</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $quizzes->links('pagination::bootstrap-5') }}
</div>
@endsection
