<!-- resources/views/adult/book/read.blade.php -->

@extends('layouts.adult')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1>{{ $book->title }} - Read</h1>
            @if($book->pdf_file)
                <iframe src="{{ asset('storage/' . $book->pdf_file) }}#toolbar=0" width="100%" height="800px"></iframe>
            @else
                <p>{{ __('No PDF available for this book.') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
