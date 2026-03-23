@extends('layouts.dashboard')

@section('title', __('Gestion des Avis'))
@section('header', __('Gestion des Avis'))

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Avis sur vos Livres') }} ({{ $reviews->total() }})</h6>
                </div>
                 <div class="col-md-6">
                    <form action="{{ route('author.reviews') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('Rechercher par livre, utilisateur, commentaire...') }}" value="{{ $search ?? '' }}">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('Utilisateur') }}</th>
                            <th>{{ __('Livre') }}</th>
                            <th class="text-center">{{ __('Note') }}</th>
                            <th>{{ __('Commentaire') }}</th>
                            <th class="text-center">{{ __('Date') }}</th>
                            {{-- <th class="text-center">{{ __('Actions') }}</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $review->user->avatar_url }}" alt="{{ $review->user->name }}" class="rounded-circle mr-3" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 font-weight-bold">{{ $review->user->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $review->book->title }}</td>
                                <td class="text-center text-nowrap">
                                     @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-gray-300' }}"></i>
                                    @endfor
                                </td>
                                <td>{{ $review->comment }}</td>
                                <td class="text-center">{{ $review->created_at->format('d/m/Y') }}</td>
                                {{-- <td class="text-center">
                                    <a href="{{ route('author.reviews.show', $review->id) }}" class="btn btn-info btn-circle btn-sm" title="{{__('Voir')}}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td> --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">{{ __('Aucun avis trouvé.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
