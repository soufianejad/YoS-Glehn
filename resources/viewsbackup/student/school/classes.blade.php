@extends('layouts.student')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">{{ __('My Classes') }}</h1>

    @if($classes->isEmpty())
        <div class="alert alert-info" role="alert">
            {{ __('You are not enrolled in any classes yet.') }}
        </div>
    @else
        <div class="row">
            @foreach($classes as $class)
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title text-primary">{{ $class->name }} ({{ $class->level ?? 'N/A' }})</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Teacher: {{ $class->teacher->name ?? 'N/A' }}</h6>
                            <p class="card-text">{{ $class->description ?? 'No description provided.' }}</p>
                            <p class="card-text small text-muted">Enrolled On: {{ \Carbon\Carbon::parse($class->pivot->enrolled_at)->format('M d, Y H:i') }}</p>

                            <hr>

                            <h6 class="mb-3">{{ __('Assigned Books:') }}</h6>
                            @if($class->bookAssignments->isEmpty())
                                <p class="text-muted">{{ __('No books assigned to this class yet.') }}</p>
                            @else
                                <ul class="list-group list-group-flush mb-3">
                                    @foreach($class->bookAssignments as $assignment)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <a href="{{ route('student.book.show', $assignment->book->slug) }}" class="text-decoration-none">
                                                    {{ $assignment->book->title }}
                                                </a>
                                                @if($assignment->due_date)
                                                    <span class="badge bg-warning text-dark ms-2">Due: {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y') }}</span>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            <div class="mt-3">
                                @if($class->teacher)
                                    <a href="{{ route('messaging.index', ['recipient_id' => $class->teacher->id]) }}" class="btn btn-sm btn-primary me-2">{{ __('Contact Teacher') }}</a>
                                @endif
                                {{-- The "View Class" button currently links to the same page, so it's removed for now.
                                     It can be re-added if a dedicated class detail page is implemented. --}}
                                {{-- <a href="{{ route('student.school.classes', $class) }}" class="btn btn-sm btn-info">{{ __('View Class') }}</a> --}}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $classes->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
