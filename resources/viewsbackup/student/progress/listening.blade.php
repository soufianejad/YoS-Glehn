<!-- resources/views/student/progress/listening.blade.php -->

@extends('layouts.student')

@section('content')
<div class="container">
    <h1>{{ __('My Listening Progress') }}</h1>

    <div class="row mb-3">
        <div class="col-md-12">
            <form action="{{ route('student.progress.listening') }}" method="GET" class="form-inline">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search books...') }}" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">{{ __('Search') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Book Title') }}</th>
                <th>{{ __('Current Position') }}</th>
                <th>{{ __('Total Duration') }}</th>
                <th>{{ __('Progress') }}</th>
                <th>{{ __('Last Listened') }}</th>
                <th>{{ __('Completed') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($audioProgress as $progress)
                <tr>
                    <td>{{ $progress->book->title ?? 'N/A' }}</td>
                    <td>{{ gmdate("H:i:s", $progress->current_position) }}</td>
                    <td>{{ gmdate("H:i:s", $progress->total_duration) }}</td>
                    <td>{{ number_format($progress->progress_percentage, 2) }}%</td>
                    <td>{{ $progress->last_listened_at->format('M d, Y H:i') }}</td>
                    <td>{{ $progress->completed_at ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $audioProgress->links('pagination::bootstrap-5') }}
</div>
@endsection
