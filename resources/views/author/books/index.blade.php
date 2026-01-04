@php
    function render_stars($rating, $max = 5) {
        $full_star = '<i class="fas fa-star text-warning"></i>';
        $half_star = '<i class="fas fa-star-half-alt text-warning"></i>';
        $empty_star = '<i class="far fa-star text-warning"></i>';
        $stars = '';
        $rating = floatval($rating);
        for ($i = 1; $i <= $max; $i++) {
            if ($rating >= $i) $stars .= $full_star;
            elseif ($rating > ($i - 1) && $rating < $i) $stars .= $half_star;
            else $stars .= $empty_star;
        }
        return $stars;
    }
@endphp

@extends('layouts.dashboard')

@section('title', __('Mes Livres'))
@section('header', __('Gestion de mes Livres'))

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Mes Livres Publiés') }} ({{ $books->total() }})</h6>
                </div>
                 <div class="col-md-5">
                    <form action="{{ route('author.books.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('Rechercher par titre...') }}" value="{{ $search ?? '' }}">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search fa-sm"></i></button>
                        </div>
                    </form>
                </div>
                <div class="col text-right">
                    <a href="{{ route('author.books.create') }}" class="btn btn-primary btn-icon-split btn-sm">
                        <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                        <span class="text">{{ __('Nouveau Livre') }}</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('Livre') }}</th>
                            <th class="text-center">{{ __('Ventes') }}</th>
                            <th class="text-center">{{ __('Avis') }}</th>
                            <th class="text-center">{{ __('Note') }}</th>
                            <th class="text-center">{{ __('Statut') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}" class="rounded mr-3" style="width: 50px; height: 70px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 font-weight-bold">{{ $book->title }}</h6>
                                            <small class="text-muted">Cat: {{ $book->category->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-success">{{ $book->sales_count ?? 0 }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-info">{{ $book->reviews_count }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    @if($book->reviews_count > 0)
                                        <div class="d-flex justify-content-center align-items-center">
                                            <span class="me-1">{{ number_format($book->reviews_avg_rating, 1) }}</span>
                                            {!! render_stars($book->reviews_avg_rating) !!}
                                        </div>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-{{ \App\Helpers\StatusHelper::bookStatusColor($book->status) }}">{{ ucfirst($book->status) }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <a href="{{ route('author.books.show', $book) }}" class="btn btn-info btn-circle btn-sm" title="{{__('Voir')}}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('author.books.edit', $book) }}" class="btn btn-warning btn-circle btn-sm" title="{{__('Modifier')}}">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <a href="{{ route('author.books.statistics', $book) }}" class="btn btn-primary btn-circle btn-sm" title="{{__('Statistiques')}}">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                    <form action="{{ route('author.books.destroy', $book) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-circle btn-sm" onclick="return confirm('{{ __('Êtes-vous sûr ?') }}')" title="{{__('Supprimer')}}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">{{ __('Aucun livre trouvé.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="d-flex justify-content-center mt-3">
                {{ $books->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
