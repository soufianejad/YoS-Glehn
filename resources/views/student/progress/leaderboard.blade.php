<!-- resources/views/student/progress/leaderboard.blade.php -->

@extends('layouts.student')

@section('content')
<div class="container">
    <h1>{{ __('Leaderboard') }}</h1>

    <div class="table-responsive">
<table class="table">
        <thead>
            <tr>
                <th>{{ __('Rank') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Points') }}</th>
                <th>{{ __('Books Read') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leaderboard as $index => $student)
                <tr @if($student->id === auth()->id()) class="table-primary fw-bold" @endif>
                    <td>
                        @if ($leaderboard->firstItem() + $index == 1)
                            <i class="fas fa-trophy text-warning"></i> 1
                        @elseif ($leaderboard->firstItem() + $index == 2)
                            <i class="fas fa-medal text-secondary"></i> 2
                        @elseif ($leaderboard->firstItem() + $index == 3)
                            <i class="fas fa-medal text-danger"></i> 3
                        @else
                            {{ $leaderboard->firstItem() + $index }}
                        @endif
                    </td>
                    <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                    <td>{{ number_format($student->points) }}</td>
                    <td>{{ $student->reading_progress_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

    {{ $leaderboard->links('pagination::bootstrap-5') }}
</div>
@endsection
