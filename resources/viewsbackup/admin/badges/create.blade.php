@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Add New Badge') }}</h1>

    <form action="{{ route('admin.badges.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.badges.form')
        <button type="submit" class="btn btn-primary">{{ __('Create Badge') }}</button>
        <a href="{{ route('admin.badges.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </form>
</div>
@endsection
