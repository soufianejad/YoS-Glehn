@extends('layouts.dashboard')

@section('title', config('app.name', 'Laravel') . ' - Parent Dashboard')

@section('content')
    @yield('content')
@endsection
