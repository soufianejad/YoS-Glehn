@extends('layouts.dashboard')

@section('title', config('app.name', 'Laravel') . ' - Student')

@section('sidebar_header')
    @php
        $user = auth()->user();
        $school = $user && $user->role === 'student' ? $user->school : null;
    @endphp
    @if($school && $school->logo)
        <div class="sidebar-header text-center py-3">
            <img src="{{ $school->logo_url }}" alt="{{ $school->name }} Logo" class="img-fluid rounded" style="max-height: 60px;">
        </div>
    @else
        @parent
    @endif
@endsection

@section('content')
    @yield('content')
@endsection