<!-- resources/views/student/school/classmates.blade.php -->

@extends('layouts.student')

@section('content')
<div class="container">
    <h1>{{ __('My Classmates') }}</h1>

    <div class="row mb-3">
        <div class="col-md-12">
            <form action="{{ route('student.school.classmates') }}" method="GET" class="form-inline">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search classmates...') }}" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">{{ __('Search') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($classmates->isEmpty())
        <p>{{ __("You don't have any classmates yet.") }}</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($classmates as $classmate)
                    <tr>
                        <td>{{ $classmate->name }}</td>
                        <td>{{ $classmate->email }}</td>
                        <td>
                            {{-- Add actions like viewing profile if applicable --}}
                            <a href="#" class="btn btn-sm btn-info">{{ __('View Profile') }}</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $classmates->links('pagination::bootstrap-5') }}
    @endif
</div>
@endsection
