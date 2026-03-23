@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Add New Tag') }}</h1>

    <form action="{{ route('admin.tags.store') }}" method="POST">
        @csrf
        @include('admin.tags.form')
        <button type="submit" class="btn btn-primary">{{ __('Create Tag') }}</button>
        <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection
