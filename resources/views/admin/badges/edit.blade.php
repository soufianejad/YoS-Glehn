@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Edit Badge') }}: {{ $badge->name }}</h1>

    <form action="{{ route('admin.badges.update', $badge) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.badges.form')
        <button type="submit" class="btn btn-primary">{{ __('Update Badge') }}</button>
        <a href="{{ route('admin.badges.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection
