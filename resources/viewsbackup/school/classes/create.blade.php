@extends('layouts.dashboard')

@section('title', __('Créer une Classe'))
@section('header', __('Créer une Nouvelle Classe'))

@section('content')
<div class="container-fluid">
     <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Détails de la Classe') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('school.classes.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Nom de la Classe') }}</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="level" class="form-label">{{ __('Niveau') }}</label>
                            <input type="text" name="level" id="level" class="form-control @error('level') is-invalid @enderror" value="{{ old('level') }}" required>
                            @error('level')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('Description') }}</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                        </div>

                         <div class="mb-3">
                            <label for="teacher_id" class="form-label">{{ __('Assigner un Enseignant (Optionnel)') }}</label>
                            <select name="teacher_id" id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror">
                                <option value="">{{ __('Aucun enseignant assigné') }}</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                            @error('teacher_id')<span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>@enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">{{ __('Créer la Classe') }}</button>
                            <a href="{{ route('school.classes.index') }}" class="btn btn-secondary">{{ __('Annuler') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
