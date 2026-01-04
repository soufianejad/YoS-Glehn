<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\Teacher\ClasseController;
use App\Http\Controllers\Teacher\BookAssignmentController;
use App\Http\Controllers\Teacher\ProgressController;


    
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
    
    // Route for managing classes
    Route::resource('classes', ClasseController::class)->only(['index', 'show']);

    // Routes for assigning books
    Route::get('/classes/{class}/assign', [BookAssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/classes/{class}/assign', [BookAssignmentController::class, 'store'])->name('assignments.store');

    // Route for tracking progress
    Route::get('/classes', [ProgressController::class, 'listClasses'])->name('progress.list-classes');
    Route::get('/classes/{class}/progress', [ProgressController::class, 'index'])->name('progress.index');
    Route::get('/quiz-attempts/{attempt}', [ProgressController::class, 'showQuizAttempt'])->name('progress.quiz-attempt');

    // Routes for creating quizzes
    Route::prefix('quizzes')->name('quizzes.')->group(function () {
        Route::get('/select-book', [\App\Http\Controllers\Teacher\QuizController::class, 'selectBook'])->name('select-book');
        Route::get('/create/{book}', [\App\Http\Controllers\Teacher\QuizController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Teacher\QuizController::class, 'store'])->name('store');
        Route::get('/{quiz}/edit', [\App\Http\Controllers\Teacher\QuizController::class, 'edit'])->name('edit');
        Route::put('/{quiz}', [\App\Http\Controllers\Teacher\QuizController::class, 'update'])->name('update');
    });


