<!-- resources/views/admin/settings/appearance.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Appearance Settings') }}</h1>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="theme" class="form-label">{{ __('Theme') }}</label>
            <input type="text" class="form-control @error('theme') is-invalid @enderror" id="theme" name="theme" value="{{ old('theme', $settings['theme'] ?? '') }}">
            @error('theme')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="logo" class="form-label">{{ __('Logo') }}</label>
            @if(!empty($settings['logo']))
                <img src="{{ asset('storage/' . $settings['logo']) }}" alt="{{ __('Current Logo') }}" class="img-thumbnail mb-2" width="150">
            @endif
            <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo">
            @error('logo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="favicon" class="form-label">{{ __('Favicon') }}</label>
            @if(!empty($settings['favicon']))
                <img src="{{ asset('storage/' . $settings['favicon']) }}" alt="{{ __('Current Favicon') }}" class="img-thumbnail mb-2" width="50">
            @endif
            <input type="file" class="form-control @error('favicon') is-invalid @enderror" id="favicon" name="favicon">
            @error('favicon')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="platform.show_prices" class="form-label">{{ __('Afficher les prix') }}</label>
            <select class="form-select @error('platform.show_prices') is-invalid @enderror" id="platform.show_prices" name="platform.show_prices">
                <option value="1" {{ old('platform.show_prices', $settings['platform.show_prices'] ?? '1') == '1' ? 'selected' : '' }}>{{ __('Oui') }}</option>
                <option value="0" {{ old('platform.show_prices', $settings['platform.show_prices'] ?? '1') == '0' ? 'selected' : '' }}>{{ __('Non') }}</option>
            </select>
            <div class="form-text">{{ __('Activer ou désactiver l\'affichage des prix des livres sur les cartes.') }}</div>
            @error('platform.show_prices')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Update Settings') }}</button>
    </form>
</div>
@endsection
