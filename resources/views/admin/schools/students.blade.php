<!-- resources/views/admin/schools/students.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>Students for {{ $school->name }}</h1>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
                <tr>
                    <td>{{ $student->id }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $student) }}" class="btn btn-sm btn-info">{{ __('View User') }}</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $students->links('pagination::bootstrap-5') }}

    <a href="{{ route('admin.schools.show', $school) }}" class="btn btn-secondary mt-3">{{ __('Back to School Details') }}</a>
</div>
@endsection
