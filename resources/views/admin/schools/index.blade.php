@extends('layouts.dashboard')

@section('title', __('Gestion des Écoles'))
@section('header', __('Gestion des Écoles'))

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Liste des Écoles') }} ({{ $schools->total() }})</h6>
                </div>
                 <div class="col-md-6">
                    <form action="{{ route('admin.schools.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="{{ __('Rechercher par nom, email...') }}" value="{{ $search ?? '' }}">
                            <select name="status" class="form-select">
                                <option value="">{{ __('Tous les statuts') }}</option>
                                <option value="approved" {{ ($status ?? '') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                                <option value="pending" {{ ($status ?? '') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="rejected" {{ ($status ?? '') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                                <option value="suspended" {{ ($status ?? '') == 'suspended' ? 'selected' : '' }}>Suspendu</option>
                            </select>
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
                            <th>{{ __('École') }}</th>
                            <th>{{ __('Contact') }}</th>
                            <th class="text-center">{{ __('Étudiants') }}</th>
                            <th class="text-center">{{ __('Statut') }}</th>
                            <th class="text-center">{{ __('Inscrit le') }}</th>
                            <th class="text-center">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schools as $school)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $school->logo_url }}" alt="{{ $school->name }}" class="rounded-circle mr-3" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0 font-weight-bold">{{ $school->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td><a href="mailto:{{ $school->email }}">{{ $school->email }}</a></td>
                                <td class="text-center">{{ $school->students()->count() }}</td>
                                <td class="text-center">
                                     <span class="badge badge-{{ \App\Helpers\StatusHelper::bookStatusColor($school->status) }}">{{ ucfirst($school->status) }}</span>
                                </td>
                                <td class="text-center">{{ $school->created_at->format('d/m/Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.schools.show', $school) }}" class="btn btn-info btn-circle btn-sm" title="{{__('Voir')}}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                     @if($school->status === 'pending')
                                        <form action="{{ route('admin.schools.approve', $school) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-circle btn-sm" title="{{__('Approuver')}}"><i class="fas fa-check"></i></button>
                                        </form>
                                        <form action="{{ route('admin.schools.reject', $school) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-circle btn-sm" title="{{__('Rejeter')}}"><i class="fas fa-times"></i></button>
                                        </form>
                                    @elseif($school->status === 'approved')
                                        <form action="{{ route('admin.schools.suspend', $school) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-circle btn-sm" title="{{__('Suspendre')}}"><i class="fas fa-minus-circle"></i></button>
                                        </form>
                                    @elseif($school->status === 'suspended' || $school->status === 'rejected')
                                        <form action="{{ route('admin.schools.approve', $school) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-circle btn-sm" title="{{__('Réactiver')}}"><i class="fas fa-check"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">{{ __('Aucune école trouvée.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $schools->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
