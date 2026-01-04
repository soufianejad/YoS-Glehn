<!-- resources/views/admin/schools/pending.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Pending Schools') }}</h1>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schools as $school)
                <tr>
                    <td>{{ $school->id }}</td>
                    <td>{{ $school->name }}</td>
                    <td>{{ $school->email }}</td>
                    <td>
                        <a href="{{ route('admin.schools.show', $school) }}" class="btn btn-sm btn-info">{{ __('View') }}</a>
                        <form action="{{ route('admin.schools.approve', $school) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this school?')">{{ __('Approve') }}</button>
                        </form>
                        <form action="{{ route('admin.schools.reject', $school) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Reject this school?')">{{ __('Reject') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $schools->links('pagination::bootstrap-5') }}
</div>
@endsection
