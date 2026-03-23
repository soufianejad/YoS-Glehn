@extends('layouts.dashboard')

@section('title', __('Mon Profil'))
@section('header', __('Profil et Paramètres'))

@section('content')
<div class="container-fluid">

    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <!-- Profile Picture Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Photo de Profil') }}</h6>
                </div>
                <div class="card-body text-center">
                    <img class="img-fluid rounded-circle mb-3" src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" style="width: 150px; height: 150px; object-fit: cover;">
                    <h4 class="font-weight-bold">{{ Auth::user()->name }}</h4>
                    <p class="text-muted">{{ ucfirst(Auth::user()->role) }}</p>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <!-- Edit Profile Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Modifier les Informations du Profil') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('reader.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="first_name" class="form-label">{{ __('Prénom') }}</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">{{ __('Nom') }}</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Adresse Email') }}</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                         <div class="mb-3">
                            <label for="avatar" class="form-label">{{ __('Changer la photo de profil') }}</label>
                            <input class="form-control" type="file" id="avatar" name="avatar">
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Mettre à jour le profil') }}</button>
                    </form>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Changer le Mot de Passe') }}</h6>
                </div>
                 <div class="card-body">
                    <form action="{{ route('reader.password.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label">{{ __('Mot de Passe Actuel') }}</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">{{ __('Nouveau Mot de Passe') }}</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">{{ __('Confirmer le Nouveau Mot de Passe') }}</label>
                            <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Changer le Mot de Passe') }}</button>
                    </form>
                </div>
            </div>

             <!-- Notification Preferences Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Préférences de Notification') }}</h6>
                </div>
                <div class="card-body">
                     <form action="{{ route('reader.notification.preferences.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        @php
                            $notificationTypes = [
                                'new_message' => __('Nouveau Message Privé'),
                                'quiz_result' => __('Résultats de Quiz'),
                                'book_purchase' => __('Confirmation d\'achat de livre'),
                                'new_subscription' => __('Confirmation d\'abonnement'),
                                'subscription_reminder' => __('Rappel d\'expiration d\'abonnement'),
                            ];
                            $channels = ['email' => __('Email'), 'site' => __('Sur le site')];
                            $userPreferences = $user->notification_preferences ?? [];
                        @endphp

                        <table class="table table-borderless">
                             @foreach ($notificationTypes as $typeKey => $typeLabel)
                                <tr>
                                    <td class="align-middle"><strong>{{ $typeLabel }}</strong></td>
                                    @foreach ($channels as $channelKey => $channelLabel)
                                        <td class="align-middle">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" role="switch" id="{{ $typeKey }}_{{ $channelKey }}" name="{{ $typeKey }}_{{ $channelKey }}" value="1"
                                                    {{ (isset($userPreferences[$typeKey][$channelKey]) && $userPreferences[$typeKey][$channelKey] === false) ? '' : 'checked' }}>
                                                <label class="form-check-label" for="{{ $typeKey }}_{{ $channelKey }}">{{ $channelLabel }}</label>
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </table>
                        <button type="submit" class="btn btn-primary mt-3">{{ __('Mettre à jour les préférences') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
