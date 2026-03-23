@extends('layouts.parent')

@section('content')
<div class="container">
    <h1>{{ __('Parent Dashboard') }}</h1>
    <p>{{ __('Welcome') }}, {{ $parent->first_name }}!</p>

    <div class="card">
        <div class="card-header">
            <h4>{{ __('My Children') }}</h4>
        </div>
        <div class="card-body">
            @if ($children->isEmpty())
                <p>{{ __('No children are currently linked to your account.') }}</p>
            @else
                <ul class="list-group">
                    @foreach ($children as $child)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-user-graduate me-2"></i>
                                <strong>{{ $child->first_name }} {{ $child->last_name }}</strong>
                                <small class="text-muted d-block">{{ __('School') }}: {{ $child->school->name ?? __('Not specified') }}</small>
                            </div>
                            <a href="{{ route('parent.child.show', $child) }}" class="btn btn-primary btn-sm">{{ __('View Progress') }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>
@endsection
