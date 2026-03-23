<!-- resources/views/school/students/show.blade.php -->

@extends('layouts.school')

@section('content')
<div class="container">
    <h1>{{ __('Student Details:') }} {{ $student->name }}</h1>

    <div class="card">
        <div class="card-header">{{ __('Student Information') }}</div>
        <div class="card-body">
            <p><strong>{{ __('ID:') }}</strong> {{ $student->id }}</p>
            <p><strong>{{ __('Name:') }}</strong> {{ $student->name }}</p>
            <p><strong>{{ __('Email:') }}</strong> {{ $student->email }}</p>
            <p><strong>{{ __('Status:') }}</strong> {{ $student->is_active ? __('Active') : __('Inactive') }}</p>
            <p><strong>{{ __('Registered At:') }}</strong> {{ $student->created_at->format('M d, Y H:i') }}</p>
            <p><strong>{{ __('Last Updated:') }}</strong> {{ $student->updated_at->format('M d, Y H:i') }}</p>
        </div>
    </div>

    <a href="{{ route('school.students.edit', $student) }}" class="btn btn-warning mt-3">{{ __('Edit Student') }}</a>
    <a href="{{ route('school.students.index') }}" class="btn btn-secondary mt-3">{{ __('Back to Students') }}</a>
</div>
@endsection
