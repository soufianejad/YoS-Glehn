@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h1 class="mb-4">{{ $page->title }}</h1>
            <div class="card shadow-sm">
                <div class="card-body">
                    {!! $page->body !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
