@extends('layouts.dashboard')

@section('title', __('Gérer les Langues'))
@section('header', __('Gérer les Langues'))

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('Ajouter une nouvelle langue') }}</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.languages.update') }}" method="POST">
                            @csrf
                            <div class="row align-items-end">
                                <div class="col-md-4 mb-3">
                                    <label for="language_code" class="form-label">{{ __('Code de la langue') }}</label>
                                    <input type="text" class="form-control @error('language_code') is-invalid @enderror" id="language_code" name="language_code" placeholder="{{ __('ex: fr, en, sw') }}" required>
                                    @error('language_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="language_name" class="form-label">{{ __('Nom de la langue') }}</label>
                                    <input type="text" class="form-control @error('language_name') is-invalid @enderror" id="language_name" name="language_name" placeholder="{{ __('ex: Français, English, Swahili') }}" required>
                                    @error('language_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <button type="submit" name="add_language" class="btn btn-primary w-100">{{ __('Ajouter la langue') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ __('Langues disponibles') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Code') }}</th>
                                        <th>{{ __('Nom') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($languages as $language)
                                        <tr>
                                            <td>{{ $language['code'] }}</td>
                                            <td>{{ $language['name'] }}</td>
                                            <td>
                                                <form action="{{ route('admin.settings.languages.update') }}" method="POST" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer cette langue ?') }}');">
                                                    @csrf
                                                    <input type="hidden" name="language_code" value="{{ $language['code'] }}">
                                                    <button type="submit" name="remove_language" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i> {{ __('Supprimer') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">{{ __('Aucune langue configurée.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
