@extends('layouts.dashboard')

@section('title', __('Modifier la Classe'))
@section('header', __('Modifier la Classe'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $class->name }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('school.classes.update', $class) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Nom de la Classe') }}</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $class->name) }}" required>
                            @error('name')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="level" class="form-label">{{ __('Niveau') }}</label>
                            <input type="text" name="level" id="level" class="form-control @error('level') is-invalid @enderror" value="{{ old('level', $class->level) }}" required>
                            @error('level')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $class->description) }}</textarea>
                            @error('description')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                        </div>

                         <div class="mb-3">
                            <label for="teacher_id" class="form-label">{{ __('Assigner un Enseignant (Optionnel)') }}</label>
                            <select name="teacher_id" id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror">
                                <option value="">{{ __('Aucun enseignant assigné') }}</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id', $class->teacher_id) == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                            @error('teacher_id')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                        </div>

                        <div class="mb-3 form-check form-switch">
                             <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $class->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">{{ __('Classe Active') }}</label>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">{{ __('Mettre à jour la Classe') }}</button>
                            <a href="{{ route('school.classes.index') }}" class="btn btn-secondary">{{ __('Annuler') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
