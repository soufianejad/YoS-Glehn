<!-- resources/views/student/book/listen.blade.php -->

@extends('layouts.student')

@section('content')
<div class="container">
    <h1>{{ $book->title }} {{ __(' - Listen') }}</h1>

    @if($book->audio_file)
        <audio controls>
            <source src="{{ asset('storage/' . $book->audio_file) }}" type="audio/mpeg">
            {{ __('Your browser does not support the audio element.') }}
        </audio>
    @else
        <p>{{ __('No audio available for this book.') }}</p>
    @endif
</div>
@endsection
