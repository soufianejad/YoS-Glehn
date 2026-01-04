@extends('layouts.dashboard')

@section('title', __('Ajouter un Étudiant'))
@section('header', __('Ajouter un Nouvel Étudiant'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Informations de l\'étudiant') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('school.students.store') }}" method="POST">
                        @csrf
                         <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">{{ __('Prénom') }}</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">{{ __('Nom') }}</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Adresse Email') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                       <div class="row">
                             <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">{{ __('Mot de passe') }}</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">{{ __('Confirmer le mot de passe') }}</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">{{ __('Ajouter l\'Étudiant') }}</button>
                            <a href="{{ route('school.students.index') }}" class="btn btn-secondary">{{ __('Annuler') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
