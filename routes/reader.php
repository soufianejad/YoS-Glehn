<?php

use App\Http\Controllers\Reader\ReaderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Reader Routes
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [ReaderController::class, 'index'])->name('dashboard');
Route::get('/favorites', [ReaderController::class, 'favorites'])->name('favorites');
Route::get('/library', [ReaderController::class, 'library'])->name('library');
Route::get('/profile', [ReaderController::class, 'profile'])->name('profile');
Route::put('/profile', [ReaderController::class, 'updateProfile'])->name('profile.update');
Route::post('/password', [ReaderController::class, 'updatePassword'])->name('password.update');
Route::put('/notifications/preferences', [ReaderController::class, 'updateNotificationPreferences'])->name('notification.preferences.update');
Route::get('/payments', [ReaderController::class, 'payments'])->name('payments');
Route::get('/subscription', [ReaderController::class, 'subscription'])->name('subscription');
Route::get('/quizzes', [ReaderController::class, 'quizzes'])->name('quizzes');
Route::get('/reviews', [ReaderController::class, 'reviews'])->name('reviews');
Route::get('/bookmarks', [ReaderController::class, 'bookmarks'])->name('bookmarks');
Route::get('/badges', [\App\Http\Controllers\Reader\BadgeController::class, 'index'])->name('badges');
Route::post('/subscription/renew', [ReaderController::class, 'renewSubscription'])->name('subscription.renew');
Route::delete('/subscription/{subscription}/cancel', [ReaderController::class, 'cancelSubscription'])->name('subscription.cancel');
