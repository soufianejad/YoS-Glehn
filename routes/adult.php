<?php

use App\Http\Controllers\Adult\DashboardController as AdultDashboardController;
use App\Http\Controllers\Adult\LibraryController as AdultLibraryController;
use App\Http\Controllers\Public\BookController;
use Illuminate\Support\Facades\Route;

// Tableau de bord
Route::get('/', [AdultDashboardController::class, 'index'])->name('dashboard');

// Bibliothèque adulte
Route::prefix('library')->name('library.')->group(function () {
    Route::get('/{book:slug}', [AdultLibraryController::class, 'show'])->name('show');
    Route::get('/{category:slug?}', [AdultLibraryController::class, 'index'])->name('index');
    Route::get('/{book:slug}/read', [BookController::class, 'read'])->name('read');
    Route::get('/{book:slug}/listen', [BookController::class, 'listen'])->name('listen');
    Route::post('/{book:slug}/review', [AdultLibraryController::class, 'storeReview'])->name('review.store');
});

// Profil adulte
Route::get('/bookmarks', [AdultDashboardController::class, 'bookmarks'])->name('bookmarks');
Route::get('/reviews', [AdultDashboardController::class, 'reviews'])->name('reviews');
Route::get('/profile', [AdultDashboardController::class, 'profile'])->name('profile');

// New routes for adult user functionalities
Route::get('/favorites', [AdultDashboardController::class, 'favorites'])->name('favorites');
Route::get('/quizzes', [AdultDashboardController::class, 'quizzes'])->name('quizzes');
Route::get('/badges', [AdultDashboardController::class, 'badges'])->name('badges');
