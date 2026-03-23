<!-- resources/views/student/school/announcements.blade.php -->

@extends('layouts.student')

@section('content')
<div class="container">
    <h1>Announcements from {{ $school->name }}</h1>

    <div class="row mb-3">
        <div class="col-md-12">
            <form action="{{ route('student.school.announcements') }}" method="GET" class="form-inline">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search announcements...') }}" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">{{ __('Search') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($announcements->isEmpty())
        <p>{{ __('No announcements from your school yet.') }}</p>
    @else
        @foreach($announcements as $announcement)
            <div class="card mb-3">
                <div class="card-header">{{ $announcement->title }}</div>
                <div class="card-body">
                    <p class="card-text">{{ $announcement->content }}</p>
                    <p class="card-text"><small class="text-muted">Published: {{ $announcement->published_at->format('M d, Y H:i') }}</small></p>
                    @if($announcement->expires_at)
                        <p class="card-text"><small class="text-muted">Expires: {{ $announcement->expires_at->format('M d, Y H:i') }}</small></p>
                    @endif
                </div>
            </div>
        @endforeach

        {{ $announcements->links('pagination::bootstrap-5') }}
    @endif
</div>
@endsection
