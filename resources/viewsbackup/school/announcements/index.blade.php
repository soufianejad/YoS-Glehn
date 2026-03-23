<!-- resources/views/school/announcements/index.blade.php -->

@extends('layouts.school')

@section('content')
<div class="container">
    <h1>{{ __('Announcements for') }} {{ $school->name }}</h1>

    <div class="row mb-3">
        <div class="col-md-12">
            <form action="{{ route('school.announcements.index') }}" method="GET" class="form-inline">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search announcements...') }}" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">{{ __('Search') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <a href="{{ route('school.announcements.create') }}" class="btn btn-primary mb-3">{{ __('Create New Announcement') }}</a>

    <table class="table">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Published At') }}</th>
                <th>{{ __('Expires At') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($announcements as $announcement)
                <tr>
                    <td>{{ $announcement->id }}</td>
                    <td>{{ $announcement->title }}</td>
                    <td>{{ $announcement->published_at->format('M d, Y H:i') }}</td>
                    <td>{{ $announcement->expires_at ? $announcement->expires_at->format('M d, Y H:i') : __('N/A') }}</td>
                    <td>
                        <a href="{{ route('school.announcements.edit', $announcement) }}" class="btn btn-sm btn-warning">{{ __('Edit') }}</a>
                        <form action="{{ route('school.announcements.destroy', $announcement) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure?') }}')">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $announcements->links('pagination::bootstrap-5') }}
</div>
@endsection
