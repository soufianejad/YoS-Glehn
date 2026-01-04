<?php

use App\Http\Controllers\MessagingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Messaging Routes
|--------------------------------------------------------------------------
|
| Here is where you can register messaging routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->prefix('messaging')->group(function () {
    Route::get('/users/messageable', [MessagingController::class, 'getMessageableUsers'])->name('messaging.users.messageable');
    Route::get('/', [MessagingController::class, 'index'])->name('messaging.index');
    Route::get('/archived', [MessagingController::class, 'archivedConversations'])->name('messaging.archived');
    Route::get('/{conversation}/new', [MessagingController::class, 'getNewMessages'])->name('messaging.new');
    Route::get('/{conversation}', [MessagingController::class, 'show'])->name('messaging.show');
    Route::post('/', [MessagingController::class, 'store'])->name('messaging.store');
    Route::post('/start', [MessagingController::class, 'storeConversationAndFirstMessage'])->name('messaging.start.post');
    Route::post('/{conversation}/toggle-read', [MessagingController::class, 'toggleReadStatus'])->name('messaging.toggleRead');
    Route::post('/{conversation}/archive', [MessagingController::class, 'archiveConversation'])->name('messaging.archive');
    Route::delete('/{conversation}', [MessagingController::class, 'destroyConversation'])->name('messaging.destroy');
});
