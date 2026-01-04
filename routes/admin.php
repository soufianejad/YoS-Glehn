<?php

use App\Http\Controllers\Admin\BookManagementController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\MessagingController as AdminMessagingController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\QuizManagementController;
use App\Http\Controllers\Admin\RevenueManagementController;
use App\Http\Controllers\Admin\SchoolManagementController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SubscriptionPlanController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes Admin - Tableau de bord et statistiques
|--------------------------------------------------------------------------
*/
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/statistics', [DashboardController::class, 'statistics'])->name('statistics');
Route::get('/activity-report', [DashboardController::class, 'activityReport'])->name('activity.report');
Route::get('/export/{type}', [DashboardController::class, 'export'])->name('export');

/*
|--------------------------------------------------------------------------
| Gestion des utilisateurs
|--------------------------------------------------------------------------
*/
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserManagementController::class, 'index'])->name('index');
    Route::get('/create', [UserManagementController::class, 'create'])->name('create');
    Route::post('/', [UserManagementController::class, 'store'])->name('store');
    Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
    Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
    Route::post('/{user}/activate', [UserManagementController::class, 'activate'])->name('activate');
    Route::post('/{user}/deactivate', [UserManagementController::class, 'deactivate'])->name('deactivate');
    Route::post('/{user}/change-role', [UserManagementController::class, 'changeRole'])->name('change-role');
    Route::post('/{user}/toggle-messages', [AdminMessagingController::class, 'toggleUserMessageReception'])->name('toggle-messages');
    Route::get('/{user}/impersonate', [UserManagementController::class, 'impersonate'])->name('impersonate');
});


/*
|--------------------------------------------------------------------------
| Gestion de la messagerie
|--------------------------------------------------------------------------
*/
Route::prefix('messaging')->name('messaging.')->group(function () {
    Route::get('/', [AdminMessagingController::class, 'index'])->name('index');
    Route::get('/{conversation}', [AdminMessagingController::class, 'show'])->name('show');
    Route::get('/{conversation}/new', [AdminMessagingController::class, 'getNewMessages'])->name('new');
    Route::delete('/{conversation}', [AdminMessagingController::class, 'destroy'])->name('destroy');
});

/*
|--------------------------------------------------------------------------
| Gestion des livres
|--------------------------------------------------------------------------
*/
Route::prefix('books')->name('books.')->group(function () {
    Route::get('/', [BookManagementController::class, 'index'])->name('index');
    Route::get('/pending', [BookManagementController::class, 'pending'])->name('pending');
    Route::get('/create', [BookManagementController::class, 'create'])->name('create');
    Route::post('/', [BookManagementController::class, 'store'])->name('store');
    Route::get('/{book}', [BookManagementController::class, 'show'])->name('show');
    Route::get('/{book}/edit', [BookManagementController::class, 'edit'])->name('edit');
    Route::put('/{book}', [BookManagementController::class, 'update'])->name('update');
    Route::delete('/{book}', [BookManagementController::class, 'destroy'])->name('destroy');

    // Validation des livres
    Route::post('/{book}/approve', [BookManagementController::class, 'approve'])->name('approve');
    Route::post('/{book}/reject', [BookManagementController::class, 'reject'])->name('reject');
    Route::post('/{book}/feature', [BookManagementController::class, 'feature'])->name('feature');
    Route::post('/{book}/change-space', [BookManagementController::class, 'changeSpace'])->name('change-space');
});

/*
|--------------------------------------------------------------------------
| Gestion des Avis
|--------------------------------------------------------------------------
*/
Route::prefix('reviews')->name('reviews.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('index');
    Route::get('/pending', [\App\Http\Controllers\Admin\ReviewController::class, 'pending'])->name('pending');
    Route::get('/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'show'])->name('show');
    Route::post('/{review}/approve', [\App\Http\Controllers\Admin\ReviewController::class, 'approve'])->name('approve');
    Route::post('/{review}/reject', [\App\Http\Controllers\Admin\ReviewController::class, 'reject'])->name('reject');
    Route::delete('/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('destroy');
});

/*
|--------------------------------------------------------------------------
| Gestion des catégories
|--------------------------------------------------------------------------
*/
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::get('/create', [CategoryController::class, 'create'])->name('create');
    Route::post('/', [CategoryController::class, 'store'])->name('store');
    Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
    Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    Route::post('/update-order', [CategoryController::class, 'updateOrder'])->name('update-order');
});

/*
|--------------------------------------------------------------------------
| Gestion des Tags
|--------------------------------------------------------------------------
*/
Route::resource('tags', \App\Http\Controllers\Admin\TagController::class)->except(['show']);

/*
|--------------------------------------------------------------------------
| Gestion des Badges
|--------------------------------------------------------------------------
*/
Route::resource('badges', \App\Http\Controllers\Admin\BadgeController::class)->except(['show']);

/*
|--------------------------------------------------------------------------
| Gestion des Annonces
|--------------------------------------------------------------------------
*/
Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class)->except(['show']);

/*
|--------------------------------------------------------------------------
| Gestion des Pages Statiques
|--------------------------------------------------------------------------
*/
Route::resource('pages', \App\Http\Controllers\Admin\PageController::class)->except(['show']);

/*
|--------------------------------------------------------------------------
| Historique des Notifications
|--------------------------------------------------------------------------
*/
Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');

/*
|--------------------------------------------------------------------------
| Gestion des écoles
|--------------------------------------------------------------------------
*/
Route::prefix('schools')->name('schools.')->group(function () {
    Route::get('/', [SchoolManagementController::class, 'index'])->name('index');
    Route::get('/pending', [SchoolManagementController::class, 'pending'])->name('pending');
    Route::get('/{school}', [SchoolManagementController::class, 'show'])->name('show');
    Route::post('/{school}/approve', [SchoolManagementController::class, 'approve'])->name('approve');
    Route::post('/{school}/reject', [SchoolManagementController::class, 'reject'])->name('reject');
    Route::post('/{school}/suspend', [SchoolManagementController::class, 'suspend'])->name('suspend');
    Route::get('/{school}/students', [SchoolManagementController::class, 'students'])->name('students');
    Route::get('/{school}/statistics', [SchoolManagementController::class, 'statistics'])->name('statistics');
});

/*
|--------------------------------------------------------------------------
| Gestion des plans d'abonnement
|--------------------------------------------------------------------------
*/
Route::prefix('subscription-plans')->name('subscription-plans.')->group(function () {
    Route::get('/', [SubscriptionPlanController::class, 'index'])->name('index');
    Route::get('/create', [SubscriptionPlanController::class, 'create'])->name('create');
    Route::post('/', [SubscriptionPlanController::class, 'store'])->name('store');
    Route::get('/{plan}/edit', [SubscriptionPlanController::class, 'edit'])->name('edit');
    Route::put('/{plan}', [SubscriptionPlanController::class, 'update'])->name('update');
    Route::delete('/{plan}', [SubscriptionPlanController::class, 'destroy'])->name('destroy');
    Route::post('/{plan}/activate', [SubscriptionPlanController::class, 'activate'])->name('activate');
    Route::post('/{plan}/deactivate', [SubscriptionPlanController::class, 'deactivate'])->name('deactivate');
});

/*
|--------------------------------------------------------------------------
| Gestion des paiements
|--------------------------------------------------------------------------
*/
Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
    Route::post('/{payment}/validate', [PaymentController::class, 'validatePayment'])->name('validate');
    Route::post('/{payment}/refund', [PaymentController::class, 'refund'])->name('refund');
    Route::get('/report/monthly', [PaymentController::class, 'monthlyReport'])->name('monthly-report');
    Route::get('/report/annual', [PaymentController::class, 'annualReport'])->name('annual-report');
});

/*
|--------------------------------------------------------------------------
| Gestion des quiz
|--------------------------------------------------------------------------
*/
Route::prefix('quiz')->name('quiz.')->group(function () {
    Route::get('/', [QuizManagementController::class, 'index'])->name('index');
    Route::get('/create/{book}', [QuizManagementController::class, 'create'])->name('create');
    Route::post('/', [QuizManagementController::class, 'store'])->name('store');
    Route::post('/generate/{book}', [QuizManagementController::class, 'generate'])->name('generate');
    Route::get('/{quiz}', [QuizManagementController::class, 'show'])->name('show');
    Route::get('/{quiz}/edit', [QuizManagementController::class, 'edit'])->name('edit');
    Route::put('/{quiz}', [QuizManagementController::class, 'update'])->name('update');
    Route::delete('/{quiz}', [QuizManagementController::class, 'destroy'])->name('destroy');
    Route::post('/{quiz}/regenerate', [QuizManagementController::class, 'regenerate'])->name('regenerate');
    Route::get('/{quiz}/results', [QuizManagementController::class, 'results'])->name('results');
});

/*
|--------------------------------------------------------------------------
| Gestion des revenus et versements
|--------------------------------------------------------------------------
*/
Route::prefix('revenues')->name('revenues.')->group(function () {
    Route::get('/', [RevenueManagementController::class, 'index'])->name('index');
    Route::get('/authors', [RevenueManagementController::class, 'authors'])->name('authors');
    Route::get('/author/{author}', [RevenueManagementController::class, 'authorDetail'])->name('author-detail');
    Route::post('/approve-period', [RevenueManagementController::class, 'approvePeriod'])->name('approve-period');
    Route::post('/distribute-subscriptions', [RevenueManagementController::class, 'distributeSubscriptions'])->name('distribute-subscriptions');

    // CRUD for individual revenue records
    Route::get('/{revenue}/edit', [RevenueManagementController::class, 'edit'])->name('edit');
    Route::put('/{revenue}', [RevenueManagementController::class, 'update'])->name('update');
    Route::post('/{revenue}/approve', [RevenueManagementController::class, 'approve'])->name('approve');
    Route::delete('/{revenue}', [RevenueManagementController::class, 'destroy'])->name('destroy');

    // Versements aux auteurs
    Route::prefix('payouts')->name('payouts.')->group(function () {
        Route::get('/', [RevenueManagementController::class, 'payouts'])->name('index');
        Route::get('/create/{author}', [RevenueManagementController::class, 'createPayout'])->name('create');
        Route::post('/', [RevenueManagementController::class, 'storePayout'])->name('store');
        Route::get('/{payout}', [RevenueManagementController::class, 'showPayout'])->name('show');
        Route::post('/{payout}/confirm', [RevenueManagementController::class, 'confirmPayout'])->name('confirm');
        Route::post('/{payout}/cancel', [RevenueManagementController::class, 'cancelPayout'])->name('cancel');
    });
});

/*
|--------------------------------------------------------------------------
| Gestion de l'espace adulte
|--------------------------------------------------------------------------
*/
Route::prefix('adult-space')->name('adult.')->group(function () {
    Route::get('/invitations', [UserManagementController::class, 'adultInvitations'])->name('invitations');
    Route::post('/generate-invitation', [UserManagementController::class, 'generateAdultInvitation'])->name('generate-invitation');
    Route::delete('/invitation/{token}', [UserManagementController::class, 'revokeInvitation'])->name('revoke-invitation');
});

/*
|--------------------------------------------------------------------------
| Paramètres de la plateforme
|--------------------------------------------------------------------------
*/
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::put('/', [SettingsController::class, 'update'])->name('update');
    Route::get('/general', [SettingsController::class, 'general'])->name('general');
    Route::get('/payment', [SettingsController::class, 'payment'])->name('payment');
    Route::get('/email', [SettingsController::class, 'email'])->name('email');
    Route::get('/appearance', [SettingsController::class, 'appearance'])->name('appearance');
    Route::post('/cache/clear', [SettingsController::class, 'clearCache'])->name('clear-cache');
    Route::post('/maintenance', [SettingsController::class, 'toggleMaintenance'])->name('toggle-maintenance');
});

/*
|--------------------------------------------------------------------------
| Jobs Manuels
|--------------------------------------------------------------------------
*/
Route::prefix('jobs')->name('jobs.')->group(function () {
    Route::get('/', function () {
        return view('admin.jobs.index');
    })->name('index');
    Route::post('/distribute-subscription-revenues', [JobController::class, 'distributeSubscriptionRevenues'])->name('distribute-subscription-revenues');
});
