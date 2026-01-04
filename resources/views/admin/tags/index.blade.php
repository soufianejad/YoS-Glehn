@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Tag Management') }}</h1>

    <a href="{{ route('admin.tags.create') }}" class="btn btn-primary mb-3">{{ __('Add New Tag') }}</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Slug') }}</th>
                <th>{{ __('Books Count') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tags as $tag)
                <tr>
                    <td>{{ $tag->id }}</td>
                    <td>{{ $tag->name }}</td>
                    <td>{{ $tag->slug }}</td>
                    <td>{{ $tag->books()->count() }}</td>
                    <td>
                        <a href="{{ route('admin.tags.edit', $tag) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                        <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __("Are you sure?") }}')">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">{{ __('No tags found.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $tags->links('pagination::bootstrap-5') }}
</div>
@endsection
