<!-- resources/views/school/dashboard/progress-report.blade.php -->

@extends('layouts.school')

@section('content')
<div class="container">
    <h1>{{ __('Student Progress Report - ') }} {{ $school->name }}</h1>

    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Student Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Avg Reading Progress') }}</th>
                <th>{{ __('Avg Audio Progress') }}</th>
                <th>{{ __('Avg Quiz Score') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ number_format($student->readingProgress->avg('progress_percentage') ?? 0, 2) }}%</td>
                    <td>{{ number_format($student->audioProgress->avg('progress_percentage') ?? 0, 2) }}%</td>
                    <td>{{ number_format($student->quizAttempts->avg('percentage') ?? 0, 2) }}%</td>
                    <td>
                        <a href="{{ route('school.students.show', $student) }}" class="btn btn-sm btn-info">{{ __('View Details') }}</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $students->links('pagination::bootstrap-5') }}
</div>
@endsection
