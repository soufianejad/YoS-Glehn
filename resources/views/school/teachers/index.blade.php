@extends('layouts.school')

@section('content')
<div class="container">
    <h1>{{ __('Teachers Management') }}</h1>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('school.teachers.create') }}" class="btn btn-primary">{{ __('Add Teacher') }}</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('First Name') }}</th>
                        <th scope="col">{{ __('Last Name') }}</th>
                        <th scope="col">{{ __('Email') }}</th>
                        <th scope="col">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teachers as $teacher)
                        <tr>
                            <th scope="row">{{ $teacher->id }}</th>
                            <td>{{ $teacher->first_name }}</td>
                            <td>{{ $teacher->last_name }}</td>
                            <td>{{ $teacher->email }}</td>
                            <td>
                                <a href="{{ route('school.teachers.edit', $teacher) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                                <form action="{{ route('school.teachers.destroy', $teacher) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this teacher?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">{{ __('No teachers found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">
        {{ $teachers->links() }}
    </div>
</div>
@endsection