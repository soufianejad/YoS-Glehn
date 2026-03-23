@extends('layouts.dashboard')

@section('title', __('Modifier l\'annonce'))
@section('header', __('Modifier l\'annonce'))

@section('content')
<div class="container">
    <h1>{{ __('Modifier l\'annonce') }}</h1>

    <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.announcements.form')
        <button type="submit" class="btn btn-primary">{{ __('Mettre à jour l\'annonce') }}</button>
        <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">{{ __('Annuler') }}</a>
    </form>
</div>
@endsection
