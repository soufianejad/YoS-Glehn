@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Edit Tag') }}: {{ $tag->name }}</h1>

    <form action="{{ route('admin.tags.update', $tag) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.tags.form')
        <button type="submit" class="btn btn-primary">{{ __('Update Tag') }}</button>
        <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection
