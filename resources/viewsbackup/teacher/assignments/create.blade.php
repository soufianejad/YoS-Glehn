@extends('layouts.dashboard')

@section('title', 'Assigner un Livre')
@section('header', 'Assigner un livre à la classe : ' . $class->name)

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('teacher.assignments.store', $class) }}" method="POST">
                @csrf

                <!-- Book Selection -->
                <div class="mb-3">
                    <label for="book_id" class="form-label">Choisir un livre</label>
                    <select id="book_id" name="book_id" class="form-select @error('book_id') is-invalid @enderror">
                        @foreach($books as $book)
                            <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                {{ $book->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('book_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Assignment Confirmation -->
                <div class="mb-3">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Vous êtes sur le point d'assigner ce livre à la classe entière : <strong>{{ $class->name }}</strong>.
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-4 text-end">
                    <a href="{{ route('teacher.classes.show', $class) }}" class="btn btn-light me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-1"></i> Assigner le livre
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
