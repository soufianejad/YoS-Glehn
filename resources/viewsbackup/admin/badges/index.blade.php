@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Badge Management') }}</h1>

    <a href="{{ route('admin.badges.create') }}" class="btn btn-primary mb-3">{{ __('Add New Badge') }}</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Icon') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Slug') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Points') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($badges as $badge)
                <tr>
                    <td>{{ $badge->id }}</td>
                    <td>
                        @if($badge->icon)
                            <img src="{{ asset('storage/' . $badge->icon) }}" alt="{{ $badge->name }}" width="50">
                        @endif
                    </td>
                    <td>{{ $badge->name }}</td>
                    <td><code>{{ $badge->slug }}</code></td>
                    <td>{{ Str::limit($badge->description, 50) }}</td>
                    <td>{{ $badge->points }}</td>
                    <td>
                        <a href="{{ route('admin.badges.edit', $badge) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                        <form action="{{ route('admin.badges.destroy', $badge) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __("Are you sure?") }}')">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">{{ __('No badges found.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $badges->links('pagination::bootstrap-5') }}
</div>
@endsection
