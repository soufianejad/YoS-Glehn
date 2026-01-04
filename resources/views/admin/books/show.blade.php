<!-- resources/views/admin/books/show.blade.php -->

@extends('layouts.dashboard')

@section('content')
<div class="container">
    <h1>{{ __('Book Details: ') . $book->title }}</h1>

    <div class="card">
        <div class="card-header">{{ __('Book Information') }}</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default_book_cover.png') }}" class="img-fluid" alt="{{ $book->title }}">
                </div>
                <div class="col-md-8">
                    <p><strong>{{ __('ID:') }}</strong> {{ $book->id }}</p>
                    <p><strong>{{ __('Title:') }}</strong> {{ $book->title }}</p>
                    <p><strong>{{ __('Slug:') }}</strong> {{ $book->slug }}</p>
                    <p><strong>{{ __('Description:') }}</strong> {{ $book->description }}</p>
                    <p><strong>{{ __('Author:') }}</strong> {{ $book->author->name ?? __('N/A') }}</p>
                    <p><strong>{{ __('Category:') }}</strong> {{ $book->category->name ?? __('N/A') }}</p>
                    <p><strong>{{ __('PDF File:') }}</strong> @if($book->pdf_file) <a href="{{ asset('storage/' . $book->pdf_file) }}" target="_blank">{{ __('View PDF') }}</a> @else {{ __('N/A') }} @endif</p>
                    <p><strong>{{ __('Audio File:') }}</strong> @if($book->audio_file) <a href="{{ asset('storage/' . $book->audio_file) }}" target="_blank">{{ __('Listen Audio') }}</a> @else {{ __('N/A') }} @endif</p>
                    <p><strong>{{ __('PDF Pages:') }}</strong> {{ $book->pdf_pages ?? __('N/A') }}</p>
                    <p><strong>{{ __('Audio Duration:') }}</strong> {{ $book->audio_duration ?? __('N/A') }} {{ __('seconds') }}</p>
                    <p><strong>{{ __('ISBN:') }}</strong> {{ $book->isbn ?? __('N/A') }}</p>
                    <p><strong>{{ __('Published Year:') }}</strong> {{ $book->published_year ?? __('N/A') }}</p>
                    <p><strong>{{ __('Language:') }}</strong> {{ $book->language ?? __('N/A') }}</p>
                    <p><strong>{{ __('Space:') }}</strong> {{ $book->space }}</p>
                    <p><strong>{{ __('Content Type:') }}</strong> {{ $book->content_type }}</p>
                    <p><strong>{{ __('PDF Price:') }}</strong> ${{ $book->pdf_price ?? '0.00' }}</p>
                    <p><strong>{{ __('Audio Price:') }}</strong> ${{ $book->audio_price ?? '0.00' }}</p>
                    <p><strong>{{ __('Status:') }}</strong> {{ $book->status }}</p>
                    <p><strong>{{ __('Created At:') }}</strong> {{ $book->created_at->format('M d, Y H:i') }}</p>
                    <p><strong>{{ __('Last Updated:') }}</strong> {{ $book->updated_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-warning mt-3">{{ __('Edit Book') }}</a>
    <a href="{{ route('admin.books.index') }}" class="btn btn-secondary mt-3">{{ __('Back to Books') }}</a>
</div>
@endsection
