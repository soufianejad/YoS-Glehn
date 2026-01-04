<!-- resources/views/admin/schools/statistics.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>Statistics for {{ $school->name }}</h1>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">{{ __('Total Students') }}</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $totalStudents }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">{{ __('Total Classes') }}</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $totalClasses }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">{{ __('Total Book Assignments') }}</div>
                <div class="card-body">
                    <h5 class="card-title">{{ $totalBookAssignments }}</h5>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.schools.show', $school) }}" class="btn btn-secondary mt-3">{{ __('Back to School Details') }}</a>
</div>
@endsection
