@extends('layouts.school')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-center">
                <div class="card-header">
                    <h1>{{ __('Student Registration QR Code') }}</h1>
                </div>
                <div class="card-body">
                    <p>{{ __('Have your students scan this QR code with their mobile device to register for your school.') }}</p>
                    
                    <div class="my-4">
                        {!! QrCode::size(250)->generate($registrationUrl) !!}
                    </div>

                    <p class="mb-0">{{ __('Your School Access Code is:') }}</p>
                    <h3 class="font-monospace bg-light d-inline-block px-3 py-2 rounded">{{ $school->access_code }}</h3>
                    
                    <hr>

                    <p><i class="fas fa-print me-2"></i><a href="javascript:window.print()">{{ __('Print this page') }}</a></p>
                </div>
                <div class="card-footer text-muted">
                    {{ __('This code is unique to') }} <strong>{{ $school->name }}</strong>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
