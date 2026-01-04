@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Create New Static Page') }}</h1>

    <form action="{{ route('admin.pages.store') }}" method="POST">
        @csrf
        @include('admin.pages.form')
        <button type="submit" class="btn btn-primary">{{ __('Create Page') }}</button>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection
