@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Static Page Management') }}</h1>

    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary mb-3">{{ __('Add New Page') }}</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Slug') }}</th>
                <th>{{ __('Published') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pages as $page)
                <tr>
                    <td>{{ $page->id }}</td>
                    <td>{{ $page->title }}</td>
                    <td>{{ $page->slug }}</td>
                    <td>
                        @if($page->is_published)
                            <span class="badge bg-success">{{ __('Yes') }}</span>
                        @else
                            <span class="badge bg-danger">{{ __('No') }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __("Are you sure?") }}')">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">{{ __('No static pages found.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $pages->links('pagination::bootstrap-5') }}
</div>
@endsection
