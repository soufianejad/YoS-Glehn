<!-- resources/views/student/progress/leaderboard.blade.php -->

@extends('layouts.student')

@section('content')
<div class="container">
    <h1>{{ __('Leaderboard') }}</h1>

    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Rank') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Books Read') }}</th>
                {{-- Add more metrics like quiz scores, badges earned, etc. --}}
            </tr>
        </thead>
        <tbody>
            @foreach($leaderboard as $student)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->reading_progress_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $leaderboard->links('pagination::bootstrap-5') }}
</div>
@endsection
