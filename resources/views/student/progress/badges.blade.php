@extends('layouts.dashboard')

@section('title', __('Mes Badges'))
@section('header', __('Mes Badges'))

@push('styles')
<style>
    .badge-card {
        border-left: 4px solid #ccc;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .badge-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15)!important;
    }
    .badge-card .badge-icon {
        font-size: 3.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('Trophées Obtenus') }}</h6>
            <div class="col-md-4">
                <form action="{{ route('student.progress.badges') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('Rechercher un badge...') }}" value="{{ $search ?? '' }}">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            @if($badges->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-award fa-4x text-gray-300 mb-3"></i>
                    <h4>{{ __("Vous n'avez pas encore gagné de badges") }}</h4>
                    <p class="text-muted">{{ __('Continuez à lire et à répondre aux quiz pour commencer votre collection !') }}</p>
                </div>
            @else
                <div class="row">
                    @foreach($badges as $badge)
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card badge-card h-100 shadow-sm" style="border-left-color: {{ $badge->color ?? '#4e73df' }};">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto">
                                            <i class="{{ $badge->icon }} badge-icon" style="color: {{ $badge->color ?? '#4e73df' }};"></i>
                                        </div>
                                        <div class="col ml-3">
                                            <h5 class="card-title font-weight-bold mb-1">{{ $badge->name }}</h5>
                                            <p class="card-text small text-muted mb-2">{{ $badge->description }}</p>
                                            <small class="text-success font-weight-bold">{{ __('Obtenu le :') }} {{ $badge->pivot->earned_at ? $badge->pivot->earned_at->format('d/m/Y') : 'N/A' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $badges->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
