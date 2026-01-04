<!-- resources/views/admin/schools/show.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>School Details: {{ $school->name }}</h1>

    <div class="card">
        <div class="card-header">{{ __('School Information') }}</div>
        <div class="card-body">
            <p><strong>ID:</strong> {{ $school->id }}</p>
            <p><strong>{{ __('Name:') }}</strong> {{ $school->name }}</p>
            <p><strong>{{ __('Email:') }}</strong> {{ $school->email }}</p>
            <p><strong>{{ __('Status:') }}</strong> {{ $school->status }}</p>
            <p><strong>{{ __('Created At:') }}</strong> {{ $school->created_at->format('M d, Y H:i') }}</p>
            <p><strong>{{ __('Last Updated:') }}</strong> {{ $school->updated_at->format('M d, Y H:i') }}</p>
        </div>
    </div>

    <div class="mt-3">
        @if($school->status === 'pending')
            <form action="{{ route('admin.schools.approve', $school) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">{{ __('Approve School') }}</button>
            </form>
            <form action="{{ route('admin.schools.reject', $school) }}" method="POST" class="d-inline ms-2">
                @csrf
                <button type="submit" class="btn btn-danger">{{ __('Reject School') }}</button>
            </form>
        @elseif($school->status === 'approved')
            <form action="{{ route('admin.schools.suspend', $school) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning">{{ __('Suspend School') }}</button>
            </form>
        @elseif($school->status === 'suspended')
            <form action="{{ route('admin.schools.approve', $school) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success">{{ __('Activate School') }}</button>
            </form>
        @endif
    </div>

    <a href="{{ route('admin.schools.index') }}" class="btn btn-secondary mt-3">{{ __('Back to Schools') }}</a>
</div>
@endsection
