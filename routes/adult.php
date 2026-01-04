<?php

use App\Http\Controllers\Adult\DashboardController as AdultDashboardController;
use App\Http\Controllers\Adult\LibraryController as AdultLibraryController;
use Illuminate\Support\Facades\Route;

// Tableau de bord
Route::get('/', [AdultDashboardController::class, 'index'])->name('dashboard');

// Bibliothèque adulte
Route::prefix('library')->name('library.')->group(function () {
    Route::get('/{category:slug?}', [AdultLibraryController::class, 'index'])->name('index');
    Route::get('/{book:slug}', [AdultLibraryController::class, 'show'])->name('show');
    Route::get('/{book:slug}/read', [AdultLibraryController::class, 'read'])->name('read');
    Route::get('/{book:slug}/listen', [AdultLibraryController::class, 'listen'])->name('listen');
});

// Achats
Route::post('/purchase/{book}/pdf', [AdultLibraryController::class, 'purchasePdf'])->name('purchase.pdf');
Route::post('/purchase/{book}/audio', [AdultLibraryController::class, 'purchaseAudio'])->name('purchase.audio');

// Profil adulte
Route::get('/bookmarks', [AdultDashboardController::class, 'bookmarks'])->name('bookmarks');
Route::get('/reviews', [AdultDashboardController::class, 'reviews'])->name('reviews');
Route::get('/profile', [AdultDashboardController::class, 'profile'])->name('profile');
