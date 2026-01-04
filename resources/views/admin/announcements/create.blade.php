@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Add New Announcement') }}</h1>

    <form action="{{ route('admin.announcements.store') }}" method="POST">
        @csrf
        @include('admin.announcements.form')
        <button type="submit" class="btn btn-primary">{{ __('Create Announcement') }}</button>
        <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection
