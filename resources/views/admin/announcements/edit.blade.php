@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Edit Announcement') }}: {{ $announcement->title }}</h1>

    <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.announcements.form')
        <button type="submit" class="btn btn-primary">{{ __('Update Announcement') }}</button>
        <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection
