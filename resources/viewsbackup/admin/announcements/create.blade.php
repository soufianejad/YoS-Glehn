@extends('layouts.dashboard')

@section('title', __('Ajouter une nouvelle annonce'))
@section('header', __('Ajouter une nouvelle annonce'))

@section('content')
<div class="container">
    <h1>{{ __('Ajouter une nouvelle annonce') }}</h1>

    <form action="{{ route('admin.announcements.store') }}" method="POST">
        @csrf
        @include('admin.announcements.form')
        <button type="submit" class="btn btn-primary">{{ __('Créer l\'annonce') }}</button>
        <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary">{{ __('Annuler') }}</a>
    </form>
</div>
@endsection
