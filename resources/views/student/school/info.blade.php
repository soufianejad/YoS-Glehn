<!-- resources/views/student/school/info.blade.php -->

@extends('layouts.student')

@section('content')
<div class="container">
    <h1>My School: {{ $school->name }}</h1>

    <div class="card">
        <div class="card-header">{{ __('School Information') }}</div>
        <div class="card-body">
            <p><strong>{{ __('Name:') }}</strong> {{ $school->name }}</p>
            <p><strong>{{ __('Email:') }}</strong> {{ $school->email }}</p>
            <p><strong>{{ __('Address:') }}</strong> {{ $school->address }}, {{ $school->city }}, {{ $school->country }}</p>
            <p><strong>{{ __('Phone:') }}</strong> {{ $school->phone }}</p>
            <p><strong>{{ __('Status:') }}</strong> {{ $school->status }}</p>
        </div>
    </div>
</div>
@endsection
