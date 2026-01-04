@extends('layouts.adult')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4 section-title">{{ __('My Profile') }}</h1>

    <div class="card">
        <div class="card-header">
            {{ __('Profile Information') }}
        </div>
        <div class="card-body">
            <p><strong>{{ __('Name:') }}</strong> {{ $user->name }}</p>
            <p><strong>{{ __('Email:') }}</strong> {{ $user->email }}</p>
            <p><strong>{{ __('Role:') }}</strong> {{ $user->role }}</p>
            {{-- Add more profile details as needed --}}
        </div>
    </div>
</div>
@endsection