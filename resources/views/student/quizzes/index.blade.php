@extends('layouts.dashboard')

@section('title', __('Liste des Quiz'))
@section('header', __('Quiz Disponibles'))

@push('styles')
<style>
    .quiz-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #e3e6f0;
    }
    .quiz-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15)!important;
    }
    .quiz-card .card-img-top {
        height: 150px;
        object-fit: cover;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Search Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('student.quiz.index') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Rechercher un quiz par titre...') }}" value="{{ $search ?? '' }}">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
    </div>
    
    @if($quizzes->isEmpty())
        <div class="text-center py-5">
             <i class="fas fa-question-circle fa-4x text-gray-300 mb-3"></i>
            <h4>{{ __('Aucun quiz disponible pour le moment') }}</h4>
            <p class="text-muted">{{ __('Revenez plus tard ou demandez à votre enseignant.') }}</p>
        </div>
    @else
        <div class="row">
            @foreach($quizzes as $quiz)
                <div class="col-lg-6 mb-4">
                    <div class="card quiz-card h-100 shadow-sm">
                        <div class="row g-0">
                            <div class="col-md-4">
                                @if($quiz->book)
                                    <img src="{{ $quiz->book->cover_image_url }}" class="card-img-top h-100" alt="{{ $quiz->book->title }}">
                                @else
                                     <div class="bg-light h-100 d-flex align-items-center justify-content-center">
                                         <i class="fas fa-book fa-3x text-gray-300"></i>
                                     </div>
                                @endif
                            </div>
                            <div class="col-md-8">
                                <div class="card-body d-flex flex-column h-100">
                                    <h5 class="card-title font-weight-bold text-gray-900">{{ $quiz->title }}</h5>
                                    @if($quiz->book)
                                        <p class="small text-muted mb-2">{{ __('Livre :') }} {{ $quiz->book->title }}</p>
                                    @endif
                                    <div class="d-flex justify-content-start text-muted small mb-3">
                                        <span class="mr-4"><i class="fas fa-question-circle mr-1"></i> {{ $quiz->questions_count }} {{ __('Questions') }}</span>
                                        <span><i class="fas fa-clock mr-1"></i> {{ $quiz->time_limit }} {{ __('min') }}</span>
                                    </div>
                                    <p class="small">{{ Str::limit($quiz->description, 100) }}</p>
                                    <div class="mt-auto">
                                        <a href="{{ route('student.quiz.show', $quiz) }}" class="btn btn-primary">{{ __('Commencer le Quiz') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $quizzes->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
