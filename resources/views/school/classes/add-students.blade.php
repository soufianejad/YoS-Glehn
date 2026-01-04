<!-- resources/views/school/classes/add-students.blade.php -->

@extends('layouts.school')

@section('content')
<div class="container">
    <h1>{{ __('Add Students to Class:') }} {{ $class->name }}</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('school.classes.add-students', $class) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="student_ids">{{ __('Select Students') }}</label>
                    <select name="student_ids[]" id="student_ids" class="form-control" multiple required>
                        @foreach($availableStudents as $student)
                            <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }} ({{ $student->email }})</option>
                        @endforeach
                    </select>
                    @error('student_ids')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">{{ __('Add Selected Students') }}</button>
                <a href="{{ route('school.classes.show', $class) }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection