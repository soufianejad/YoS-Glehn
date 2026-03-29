@extends('layouts.dashboard')

@section('title', config('app.name', 'Laravel') . ' - Student')

@section('styles')
@parent
    @php
        $user = auth()->user();
        $school = $user && $user->role === 'student' ? $user->school : null;
    @endphp
    @if($school && $school->primary_color)
        <style>
            :root {
                --bs-primary: {{ $school->primary_color }};
                --bs-primary-rgb: {{ hexdec(substr($school->primary_color, 1, 2)) }}, {{ hexdec(substr($school->primary_color, 3, 2)) }}, {{ hexdec(substr($school->primary_color, 5, 2)) }};
            }
            #sidebar {
                background: var(--bs-primary) !important;
                color: #fff;
            }
            .btn-primary, .bg-primary {
                background-color: var(--bs-primary) !important;
                border-color: var(--bs-primary) !important;
            }
        </style>
    @endif
@endsection

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