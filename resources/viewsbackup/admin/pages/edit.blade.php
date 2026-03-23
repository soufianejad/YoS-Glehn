@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Edit Static Page') }}: {{ $page->title }}</h1>

    <form action="{{ route('admin.pages.update', $page) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.pages.form')
        <button type="submit" class="btn btn-primary">{{ __('Update Page') }}</button>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection
