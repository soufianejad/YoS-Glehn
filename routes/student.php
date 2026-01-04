<?php

use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\LibraryController;
use App\Http\Controllers\Student\ProgressController;
use App\Http\Controllers\Student\QuizController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Étudiant - Tableau de bord
|--------------------------------------------------------------------------
*/
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');

/*
|--------------------------------------------------------------------------
| Bibliothèque éducative
|--------------------------------------------------------------------------
*/
Route::prefix('library')->name('library.')->group(function () {
    Route::get('/{category:slug?}', [LibraryController::class, 'index'])->name('index'); // Updated index route
    Route::get('/recommended', [LibraryController::class, 'recommended'])->name('recommended');
    Route::get('/assigned', [LibraryController::class, 'assigned'])->name('assigned');
    Route::get('/search', [LibraryController::class, 'search'])->name('search');
});

/*
|--------------------------------------------------------------------------
| Lecture et écoute
|--------------------------------------------------------------------------
*/
Route::prefix('book')->name('book.')->group(function () {
    Route::get('/{book:slug}', [LibraryController::class, 'show'])->name('show');
    Route::get('/{book:slug}/listen', [LibraryController::class, 'listen'])->name('listen');
    Route::post('/{book}/audio-progress', [LibraryController::class, 'updateAudioProgress'])->name('audio-progress');
    Route::post('/{book}/download', [LibraryController::class, 'download'])->name('download');
});

/*
|--------------------------------------------------------------------------
| Quiz et exercices
|--------------------------------------------------------------------------
*/
Route::prefix('quiz')->name('quiz.')->group(function () {
    Route::get('/', [QuizController::class, 'index'])->name('index');
    Route::get('/{quiz}', [QuizController::class, 'show'])->name('show');
    Route::match(['get', 'post'], '/{quiz}/start', [QuizController::class, 'start'])->name('start');
    Route::post('/{quiz}/submit', [QuizController::class, 'submit'])->name('submit');
    Route::get('/{attempt}/results', [QuizController::class, 'results'])->name('results');
    Route::get('/book/{book}', [QuizController::class, 'bookQuiz'])->name('book-quiz');
});

/*
|--------------------------------------------------------------------------
| Progression et statistiques
|--------------------------------------------------------------------------
*/
Route::prefix('progress')->name('progress.')->group(function () {
    Route::get('/', [ProgressController::class, 'index'])->name('index');
    Route::get('/reading', [ProgressController::class, 'reading'])->name('reading');
    Route::get('/listening', [ProgressController::class, 'listening'])->name('listening');
    Route::get('/quizzes', [ProgressController::class, 'quizzes'])->name('quizzes');
    Route::get('/badges', [ProgressController::class, 'badges'])->name('badges');
    Route::get('/leaderboard', [ProgressController::class, 'leaderboard'])->name('leaderboard');
});

/*
|--------------------------------------------------------------------------
| Espace école
|--------------------------------------------------------------------------
*/
Route::prefix('my-school')->name('school.')->group(function () {
    Route::get('/', [DashboardController::class, 'school'])->name('info');
    Route::get('/classes', [DashboardController::class, 'classes'])->name('classes');
    Route::get('/announcements', [DashboardController::class, 'announcements'])->name('announcements');
    Route::get('/classmates', [DashboardController::class, 'classmates'])->name('classmates');
});
