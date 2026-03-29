@extends('layouts.teacher')

@section('title', 'Gestion des Quiz')
@section('header', 'Gestion des Quiz')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0 font-weight-bold text-primary">{{ __('Liste des Quiz') }}</h2>
        <a href="{{ route('teacher.quizzes.select-book') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> {{ __('Créer un Quiz') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">{{ __('Titre') }}</th>
                            <th>{{ __('Livre Associé') }}</th>
                            <th>{{ __('Questions') }}</th>
                            <th>{{ __('Score Min.') }}</th>
                            <th>{{ __('Statut') }}</th>
                            <th class="pe-4 text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quizzes as $quiz)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $quiz->title }}</td>
                                <td>{{ $quiz->book->title ?? __('N/A') }}</td>
                                <td>{{ $quiz->questions_count ?? $quiz->questions()->count() }}</td>
                                <td>{{ $quiz->pass_score }}%</td>
                                <td>
                                    @if($quiz->is_active)
                                        <span class="badge bg-success">{{ __('Actif') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Inactif') }}</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Modifier') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('teacher.quizzes.destroy', $quiz) }}" method="POST" class="d-inline-block" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer ce quiz ?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Supprimer') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-clipboard-list fa-3x mb-3 d-block text-secondary opacity-50"></i>
                                    {{ __('Aucun quiz trouvé.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($quizzes->hasPages())
            <div class="card-footer bg-white border-0 pt-4 pb-2">
                {{ $quizzes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
