@extends('layouts.dashboard')

@section('title', __('Gestion globale des annonces'))
@section('header', __('Gestion globale des annonces'))

@section('content')
<div class="container">
    <h1>{{ __('Gestion globale des annonces') }}</h1>

    <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary mb-3">{{ __('Ajouter une nouvelle annonce') }}</a>

    <div class="table-responsive">
<table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Titre') }}</th>
                <th>{{ __('École') }}</th>
                <th>{{ __('Publié le') }}</th>
                <th>{{ __('Expire le') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($announcements as $announcement)
                <tr>
                    <td>{{ $announcement->id }}</td>
                    <td>{{ $announcement->title }}</td>
                    <td>
                        @if($announcement->school)
                            <a href="{{ route('admin.schools.show', $announcement->school) }}">{{ $announcement->school->name }}</a>
                        @else
                            <span class="badge bg-info">{{ __('Global') }}</span>
                        @endif
                    </td>
                    <td>{{ $announcement->published_at ? $announcement->published_at->format('d/m/Y H:i') : __('N/A') }}</td>
                    <td>{{ $announcement->expires_at ? $announcement->expires_at->format('d/m/Y H:i') : __('Never') }}</td>
                    <td>
                        <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-sm btn-warning">{{ __('Modifier') }}</a>
                        <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Êtes-vous sûr ?') }}')">{{ __('Supprimer') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">{{ __('Aucune annonce trouvée.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

    {{ $announcements->links('pagination::bootstrap-5') }}
</div>
@endsection

