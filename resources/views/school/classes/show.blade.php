<!-- resources/views/school/classes/show.blade.php -->

@extends('layouts.school')

@section('content')
<div class="container">
    <h1>{{ __('Class Details:') }} {{ $class->name }}</h1>

    <div class="card mb-4">
        <div class="card-header">{{ __('Class Information') }}</div>
        <div class="card-body">
            <p><strong>{{ __('ID:') }}</strong> {{ $class->id }}</p>
            <p><strong>{{ __('Name:') }}</strong> {{ $class->name }}</p>
            <p><strong>{{ __('Slug:') }}</strong> {{ $class->slug }}</p>
            <p><strong>{{ __('Description:') }}</strong> {{ $class->description ?? __('N/A') }}</p>
            <p><strong>{{ __('Level:') }}</strong> {{ $class->level ?? __('N/A') }}</p>
            <p><strong>{{ __('Students Count:') }}</strong> {{ $class->students_count }}</p>
            <p><strong>{{ __('Active:') }}</strong> {{ $class->is_active ? __('Yes') : __('No') }}</p>
            <p><strong>{{ __('Created At:') }}</strong> {{ $class->created_at ? $class->created_at->format('M d, Y H:i') : __('N/A') }}</p>
            <p><strong>{{ __('Last Updated:') }}</strong> {{ $class->updated_at ? $class->updated_at->format('M d, Y H:i') : __('N/A') }}</p>
        </div>
    </div>

    <ul class="nav nav-tabs" id="classTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="students-tab" data-bs-toggle="tab" data-bs-target="{{ __('students') }}" type="button" role="tab" aria-controls="students" aria-selected="true">{{ __('Students') }}</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="statistics-tab" data-bs-toggle="tab" data-bs-target="{{ __('statistics') }}" type="button" role="tab" aria-controls="statistics" aria-selected="false">{{ __('Statistics') }}</button>
        </li>
    </ul>

    <div class="tab-content" id="classTabContent">
        <div class="tab-pane fade show active" id="students" role="tabpanel" aria-labelledby="students-tab">
            <div class="card">
                <div class="card-body">
                    <h2>{{ __('Students in this Class') }}</h2>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Enrolled At') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($class->students as $student)
                                <tr>
                                    <td>{{ $student->id }}</td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>
                                        {{ $student->pivot->enrolled_at 
                                            ? \Carbon\Carbon::parse($student->pivot->enrolled_at)->format('M d, Y H:i') 
                                            : __('N/A') }}
                                    </td>
                                    
                                    <td>
                                        <form action="{{ route('school.classes.remove-student', ['class' => $class, 'student' => $student]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Remove this student from class?') }}')">{{ __('Remove') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="statistics" role="tabpanel" aria-labelledby="statistics-tab">
            <div class="card">
                <div class="card-body">
                    <h2>{{ __('Class Statistics') }}</h2>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Completed Books') }}</h5>
                                    <p class="card-text fs-4">{{ $class->completed_books_count }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Total Reading Time') }}</h5>
                                    <p class="card-text fs-4">{{ round($class->total_reading_time / 3600, 2) }} {{ __('hours') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Average Quiz Score') }}</h5>
                                    <p class="card-text fs-4">{{ round($class->average_quiz_score, 2) }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('school.classes.edit', $class) }}" class="btn btn-warning mt-3">{{ __('Edit Class') }}</a>
    <a href="{{ route('school.classes.add-students-form', $class) }}" class="btn btn-primary mt-3">{{ __('Add Students') }}</a>
    <a href="{{ route('school.classes.index') }}" class="btn btn-secondary mt-3">{{ __('Back to Classes') }}</a>
</div>
@endsection
