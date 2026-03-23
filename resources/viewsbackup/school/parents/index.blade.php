@extends('layouts.school')

@section('content')
<div class="container">
    <h1>{{ __('Parents Management') }}</h1>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('school.parents.create') }}" class="btn btn-primary">{{ __('Add Parent') }}</a>
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
                        <th scope="col">{{ __('Linked Children') }}</th>
                        <th scope="col">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($parents as $parent)
                        <tr>
                            <th scope="row">{{ $parent->id }}</th>
                            <td>{{ $parent->first_name }}</td>
                            <td>{{ $parent->last_name }}</td>
                            <td>{{ $parent->email }}</td>
                            <td>
                                @forelse ($parent->children as $child)
                                    <span class="badge bg-secondary">{{ $child->first_name }} {{ $child->last_name }}</span>
                                @empty
                                    <span class="badge bg-light text-dark">{{ __('No children linked') }}</span>
                                @endforelse
                            </td>
                            <td>
                                <a href="{{ route('school.parents.edit', $parent) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                                <form action="{{ route('school.parents.destroy', $parent) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this parent? This will not delete the student accounts.') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">{{ __('No parents found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">
        {{ $parents->links() }}
    </div>
</div>
@endsection