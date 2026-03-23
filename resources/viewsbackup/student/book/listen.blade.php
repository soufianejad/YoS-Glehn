<!-- resources/views/student/book/listen.blade.php -->

@extends('layouts.student')

@section('content')
<div class="container">
    <h1>{{ $book->title }} - {{ __('Listen') }}</h1>

    @if($book->audio_file)
        <audio id="audioPlayer" controls controlsList="nodownload">
            <source src="{{ asset('storage/' . $book->audio_file) }}" type="audio/mpeg">
            {{ __("Votre navigateur ne supporte pas l'élément audio.") }}
        </audio>
    @else
        <p>{{ __("Aucun fichier audio n'est disponible pour ce livre.") }}</p>
    @endif
</div>
@endsection
