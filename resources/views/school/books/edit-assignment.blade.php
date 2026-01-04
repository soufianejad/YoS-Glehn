@extends('layouts.dashboard')

@section('title', __('Modifier l\'Assignation'))
@section('header', __('Modifier l\'Assignation'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                 <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Détails de l\'assignation') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('school.books.assignments.update', $assignment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="book_id" class="form-label">{{ __('Livre') }}</label>
                            <input type="text" class="form-control" value="{{ $assignment->book->title }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="class_id" class="form-label">{{ __('Classe') }}</label>
                             <input type="text" class="form-control" value="{{ $assignment->class->name }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="due_date" class="form-label">{{ __('Date Limite (Optionnel)') }}</label>
                            <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror" id="due_date" name="due_date" value="{{ old('due_date', $assignment->due_date ? $assignment->due_date->format('Y-m-d\TH:i') : '') }}">
                             @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">{{ __('Notes (Optionnel)') }}</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $assignment->notes) }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3 form-check form-switch">
                            <input type="hidden" name="is_mandatory" value="0">
                            <input class="form-check-input" type="checkbox" id="is_mandatory" name="is_mandatory" value="1" {{ old('is_mandatory', $assignment->is_mandatory) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_mandatory">{{ __('Obligatoire') }}</label>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">{{ __('Mettre à jour') }}</button>
                            <a href="{{ route('school.books.assignments.index') }}" class="btn btn-secondary">{{ __('Annuler') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection