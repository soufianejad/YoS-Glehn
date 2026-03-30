@extends('layouts.student')

@section('content')
<div class="container">
    <h1>{{ __('Mes Certificats') }}</h1>
    <p class="text-muted">{{ __('Téléchargez vos certificats virtuels gagnés grâce à vos accomplissements.') }}</p>

    <div class="row mt-4">
        @foreach($certificates as $cert)
        <div class="col-md-4 mb-4">
            <div class="card h-100 {{ $cert['is_earned'] ? 'border-success' : 'border-secondary opacity-50' }}">
                <div class="card-body text-center">
                    <i class="fas fa-certificate fa-4x mb-3 {{ $cert['is_earned'] ? 'text-success' : 'text-secondary' }}"></i>
                    <h5 class="card-title">{{ $cert['title'] }}</h5>
                    <p class="card-text">{{ $cert['description'] }}</p>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-center pb-3">
                    @if($cert['is_earned'])
                        <a href="{{ route('student.progress.certificates.generate', $cert['id']) }}" class="btn btn-success" target="_blank">
                            <i class="fas fa-download"></i> {{ __('Télécharger (PDF)') }}
                        </a>
                    @else
                        <button class="btn btn-secondary disabled">
                            <i class="fas fa-lock"></i> {{ __('Non débloqué') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
