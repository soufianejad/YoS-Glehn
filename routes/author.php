<?php

use App\Http\Controllers\Author\BookController as AuthorBookController;
use App\Http\Controllers\Author\DashboardController as AuthorDashboardController;
use App\Http\Controllers\Author\RevenueController;
use Illuminate\Support\Facades\Route;

// Tableau de bord
Route::get('/', [AuthorDashboardController::class, 'index'])->name('dashboard');
Route::get('/statistics', [AuthorDashboardController::class, 'statistics'])->name('statistics');
Route::get('/reviews', [AuthorDashboardController::class, 'reviews'])->name('reviews');
Route::get('/reviews/{review}', [AuthorDashboardController::class, 'showReview'])->name('reviews.show');

// Gestion des livres
Route::prefix('books')->name('books.')->group(function () {
    Route::get('/', [AuthorBookController::class, 'index'])->name('index');
    Route::get('/create', [AuthorBookController::class, 'create'])->name('create');
    Route::post('/', [AuthorBookController::class, 'store'])->name('store');
    Route::get('/{book}', [AuthorBookController::class, 'show'])->name('show');
    Route::get('/{book}/edit', [AuthorBookController::class, 'edit'])->name('edit');
    Route::put('/{book}', [AuthorBookController::class, 'update'])->name('update');
    Route::delete('/{book}', [AuthorBookController::class, 'destroy'])->name('destroy');
    Route::get('/{book}/statistics', [AuthorBookController::class, 'statistics'])->name('statistics');
});

// Revenus
Route::prefix('revenues')->name('revenues.')->group(function () {
    Route::get('/', [RevenueController::class, 'index'])->name('index');
    Route::get('/details', [RevenueController::class, 'details'])->name('details');
    Route::get('/history', [RevenueController::class, 'history'])->name('history');
    Route::get('/report/{year}/{month}', [RevenueController::class, 'monthlyReport'])->name('monthly-report');

    // Payout Request
    Route::get('/request-payout', [RevenueController::class, 'showPayoutRequestForm'])->name('payout.request');
    Route::post('/request-payout', [RevenueController::class, 'submitPayoutRequest'])->name('payout.submit');
});

// Profil auteur
Route::get('/profile', [AuthorDashboardController::class, 'profile'])->name('profile');
Route::put('/profile', [AuthorDashboardController::class, 'updateProfile'])->name('profile.update');
