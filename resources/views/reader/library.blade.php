@extends('layouts.dashboard')

@section('title', __('Ma Bibliothèque'))
@section('header', __('Ma Bibliothèque'))

@push('styles')
<style>
    .book-card .card-img-top {
        height: 250px;
        object-fit: cover;
    }
    .empty-state {
        text-align: center;
        padding: 4rem;
        border: 2px dashed #e3e6f0;
        border-radius: 0.75rem;
    }
    .empty-state i {
        font-size: 4rem;
        color: #e3e6f0;
    }
    .filter-card {
        border-radius: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Filter and Search Form -->
    <div class="card shadow-sm mb-4 filter-card">
        <div class="card-body">
            <form action="{{ route('reader.library') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label font-weight-bold">{{ __('Rechercher par Titre') }}</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="e.g., Le Prince">
                </div>
                <div class="col-md-3">
                    <label for="reading_status" class="form-label font-weight-bold">{{ __('Statut') }}</label>
                    <select class="form-select" id="reading_status" name="reading_status">
                        <option value="">{{ __('Tous') }}</option>
                        <option value="not_started" {{ request('reading_status') == 'not_started' ? 'selected' : '' }}>{{ __('Non commencé') }}</option>
                        <option value="in_progress" {{ request('reading_status') == 'in_progress' ? 'selected' : '' }}>{{ __('En cours') }}</option>
                        <option value="finished" {{ request('reading_status') == 'finished' ? 'selected' : '' }}>{{ __('Terminé') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="access_type" class="form-label font-weight-bold">{{ __('Accès') }}</label>
                    <select class="form-select" id="access_type" name="access_type">
                        <option value="">{{ __('Tous') }}</option>
                        <option value="purchased" {{ request('access_type') == 'purchased' ? 'selected' : '' }}>{{ __('Acheté') }}</option>
                        <option value="subscription" {{ request('access_type') == 'subscription' ? 'selected' : '' }}>{{ __('Abonnement') }}</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">{{ __('Filtrer') }}</button>
                </div>
            </form>
        </div>
    </div>

    @if($purchases->isEmpty())
        <div class="empty-state bg-white shadow-sm mt-5">
            <i class="fas fa-book-shelf mb-4"></i>
            <h3 class="text-gray-800">{{ __("Votre bibliothèque est vide") }}</h3>
            <p class="text-muted">{{ __("Les livres que vous achetez ou auxquels vous accédez via votre abonnement apparaîtront ici.") }}</p>
            <a href="{{ route('library.index') }}" class="btn btn-primary mt-3">
                <i class="fas fa-search mr-2"></i> {{ __('Explorer et trouver des livres') }}
            </a>
        </div>
    @else
        <div class="row">
            @foreach($purchases as $purchase)
                @if($purchase->book)
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="card book-card h-100 shadow-sm border-0">
                            <a href="{{ route('book.show', $purchase->book->slug) }}">
                                <img src="{{ $purchase->book->cover_image_url }}" class="card-img-top" alt="{{ $purchase->book->title }}">
                            </a>
                            <div class="card-body d-flex flex-column p-3">
                                <h6 class="card-title font-weight-bold mb-1"><a href="{{ route('book.show', $purchase->book->slug) }}" class="text-gray-900">{{ Str::limit($purchase->book->title, 40) }}</a></h6>
                                @if($purchase->book->author)
                                    <p class="small text-muted mb-2">{{ $purchase->book->author->name }}</p>
                                @endif

                                @php
                                    $progress = $purchase->book->readingProgress->where('user_id', auth()->id())->first();
                                @endphp

                                @if($progress && $progress->progress_percentage > 0)
                                    <div class="progress" style="height: 5px;" title="{{ round($progress->progress_percentage) }}% terminé">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $progress->progress_percentage }}%;" aria-valuenow="{{ $progress->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                @endif

                                <div class="mt-auto pt-3">
                                    <a href="{{ route('read.book', $purchase->book) }}" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-book-open me-1"></i> 
                                        {{ ($progress && $progress->progress_percentage > 0) ? __('Continuer') : __('Commencer la lecture') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $purchases->links() }}
        </div>
    @endif
</div>
@endsection
