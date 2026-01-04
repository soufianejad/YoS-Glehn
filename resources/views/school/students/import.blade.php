@extends('layouts.school')

@section('content')
<div class="container">
    <h1>{{ __('Import Students') }}</h1>

    <div class="alert alert-info" role="alert">
        <h4 class="alert-heading">{{ __('Instructions') }}</h4>
        <p>{{ __('To import students, please prepare a file in CSV, XLS, or XLSX format. The first row must be a header containing the exact column names:') }} <code>first_name</code>, <code>last_name</code>, <code>{{ __('email') }}</code>, {{ __('and') }} <code>{{ __("password") }}</code>.</p>
        <hr>
        <p class="mb-0">{{ __('To ensure the format is correct, we highly recommend downloading our template.') }}
            <a href="{{ route('school.students.import.template') }}" class="btn btn-sm btn-outline-primary ms-2"><i class="fas fa-download me-1"></i> {{ __('Download Template') }}</a>
        </p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('school.students.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="students_file" class="form-label">{{ __('Select File to Upload') }}</label>
                    <input type="file" class="form-control @error('students_file') is-invalid @enderror" id="students_file" name="students_file" required>
                    @error('students_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload me-2"></i> {{ __('Import Students') }}</button>
                <a href="{{ route('school.students.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
            </form>

            @if (session('error'))
                <div class="alert alert-danger mt-3">
                    {!! session('error') !!}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection