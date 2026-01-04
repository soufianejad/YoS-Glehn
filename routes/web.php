<?php

use App\Http\Controllers\Adult\InvitationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\ChangeLanguageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Public\BookController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\AuthorController;
use App\Http\Controllers\Public\LibraryController; // New: Public PageController
use App\Http\Controllers\Public\SubscriptionController;
use App\Http\Controllers\User\NotificationPreferencesController;
use App\Http\Controllers\Admin\UserManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes - Espace Public
|--------------------------------------------------------------------------
*/

// Language Switcher
Route::get('lang/{locale}', [ChangeLanguageController::class, 'changeLocale'])->name('change.language');

// Page d'accueil publique
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auteur public profile
Route::get('/authors/{author}', [AuthorController::class, 'show'])->name('public.author.show');

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Bibliothèque publique
Route::prefix('library')->name('library.')->group(function () {
    Route::get('/{category:slug?}', [LibraryController::class, 'index'])->name('index'); // Updated index route
    Route::get('/search', [LibraryController::class, 'search'])->name('search');
    Route::get('/popular', [LibraryController::class, 'popular'])->name('popular');
    Route::get('/recent', [LibraryController::class, 'recent'])->name('recent');
});

// Détails d'un livre
Route::prefix('book')->name('book.')->group(function () {
    Route::get('/{book:slug}', [BookController::class, 'show'])->name('show');
    Route::post('/{book}/increment-views', [BookController::class, 'incrementViews'])->name('increment-views');
});

// Authentification
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Student Registration with Access Code
    Route::get('/student-registration', [App\Http\Controllers\StudentRegistrationController::class, 'create'])->name('student.register');
    Route::post('/student-registration', [App\Http\Controllers\StudentRegistrationController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Routes protégées (lecteurs authentifiés)
Route::middleware(['auth'])->group(function () {

    // Page dédiée aux marque-pages
    Route::get('/my-bookmarks', [BookmarkController::class, 'showAll'])->name('bookmarks.showAll');

    // Lecture de livres
    Route::prefix('read')->name('read.')->group(function () {
        Route::get('/{book:slug}', [BookController::class, 'read'])->name('book');
        Route::get('/secure-pdf/{book:slug}', [BookController::class, 'servePdfContent'])->name('pdf.content');
        Route::post('/{book}/progress', [BookController::class, 'updateReadingProgress'])->name('progress');
        // Route::post('/{book}/download', [BookController::class, 'download'])->name('download');
    });

    // Gestion des marque-pages (bookmarks)
    Route::prefix('books/{book}/bookmarks')->name('bookmarks.')->group(function () {
        Route::get('/', [BookmarkController::class, 'index'])->name('index');
        Route::post('/', [BookmarkController::class, 'store'])->name('store');
    });
    Route::prefix('bookmarks/{bookmark}')->group(function () {
        Route::put('/', [BookmarkController::class, 'update'])->name('bookmarks.update');
        Route::delete('/', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');
    });

    // Écoute audio
    Route::prefix('listen')->name('listen.')->group(function () {
        Route::get('/{book:slug}', [BookController::class, 'listen'])->name('book');
        Route::post('/{book}/progress', [BookController::class, 'updateAudioProgress'])->name('progress');
    });

    // Favoris
    Route::post('/favorites/{book}/toggle', [App\Http\Controllers\FavoriteController::class, 'toggleFavorite'])->name('favorites.toggle');

    // Avis
    Route::post('/book/{book}/review', [App\Http\Controllers\Public\BookController::class, 'storeReview'])->name('review.store');
    Route::put('/review/{review}', [App\Http\Controllers\Public\BookController::class, 'updateReview'])->name('review.update');
    Route::delete('/review/{review}', [App\Http\Controllers\Public\BookController::class, 'deleteReview'])->name('review.destroy');

    // Achats individuels
    Route::post('/purchase/{book}/pdf', [App\Http\Controllers\Public\BookController::class, 'purchasePdf'])->name('purchase.pdf');
    Route::post('/purchase/{book}/audio', [App\Http\Controllers\Public\BookController::class, 'purchaseAudio'])->name('purchase.audio');
    Route::get('/book/{book}/secure-download', [BookController::class, 'secureDownload'])->name('book.secure_download');

    // Tableau de bord utilisateur
    Route::get('/dashboard', [App\Http\Controllers\Public\HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [App\Http\Controllers\Public\HomeController::class, 'redirectToProfile'])->name('profile');

    // General Quiz Routes
    Route::middleware(['auth'])->prefix('quiz')->name('quiz.')->group(function () {
        Route::get('/book/{book}', [App\Http\Controllers\QuizController::class, 'show'])->name('show');
        Route::get('/{quiz}/start', [App\Http\Controllers\QuizController::class, 'start'])->name('start');
        Route::post('/{quiz}', [App\Http\Controllers\QuizController::class, 'submit'])->name('submit');
        Route::get('/result/{attempt}', [App\Http\Controllers\QuizController::class, 'result'])->name('result');
    });

});
// Abonnements
Route::prefix('subscriptions')->name('subscription.')->group(function () {
    Route::get('/', [SubscriptionController::class, 'index'])->name('index');
    Route::get('/plans', [SubscriptionController::class, 'plans'])->name('plans');
    Route::post('/subscribe/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscribe');
    Route::post('/renew', [SubscriptionController::class, 'renew'])->name('renew');
    Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
});
/*
|--------------------------------------------------------------------------
| Routes Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    require __DIR__.'/admin.php';
});
Route::get('/users/stop-impersonating', [UserManagementController::class, 'stopImpersonating'])->name('users.stop-impersonating');

/*
|--------------------------------------------------------------------------
| Routes Auteur
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'author'])->prefix('author')->name('author.')->group(function () {
    require __DIR__.'/author.php';
});

/*
|--------------------------------------------------------------------------
| Routes École
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'school'])->prefix('school')->name('school.')->group(function () {
    require __DIR__.'/school.php';
});

/*
|--------------------------------------------------------------------------
| Routes Étudiant
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    require __DIR__.'/student.php';
});

/*
|--------------------------------------------------------------------------
| Routes Professeur
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    require __DIR__.'/teacher.php';
});

/*
|--------------------------------------------------------------------------
| Routes Lecteur
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'consumer'])->prefix('reader')->name('reader.')->group(function () {
    require __DIR__.'/reader.php';
});

/*
|--------------------------------------------------------------------------
| Routes Parent
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'parent'])->prefix('parent')->name('parent.')->group(function () {
    require __DIR__.'/parent.php';
});

/*
|--------------------------------------------------------------------------
| Routes Messagerie
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    require __DIR__.'/messaging.php';
});

/*
|--------------------------------------------------------------------------
| Routes Espace Adulte (Accès restreint)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'adult_access'])->prefix('adult')->name('adult.')->group(function () {
    require __DIR__.'/adult.php';
});

// Inscription espace adulte via invitation
Route::get('/adult-invitation/{token}', [InvitationController::class, 'showRegistrationForm'])->name('adult.invitation');
Route::post('/adult-registration/{token}', [InvitationController::class, 'register'])->name('adult.register');

/*
|--------------------------------------------------------------------------
| API Routes (handled by Web middleware group for stateful auth)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->name('api.')->middleware('auth')->group(function () {
    Route::get('/notifications', [App\Http\Controllers\Api\NotificationApiController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-as-read', [App\Http\Controllers\Api\NotificationApiController::class, 'markAsRead'])->name('notifications.markAsRead');
    
    // Messaging API routes
    Route::prefix('messaging')->name('messaging.')->group(function () {
        Route::get('/{conversation}', [App\Http\Controllers\Api\MessagingApiController::class, 'show'])->name('show');
        Route::get('/{conversation}/new', [App\Http\Controllers\Api\MessagingApiController::class, 'getNewMessages'])->name('new');
    });
});

// Specific Static Pages
Route::view('/about', 'public.about')->name('about');
Route::view('/faq', 'public.faq')->name('faq');

// Generic Static Pages (CMS) - MUST BE LAST
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show');

route::middleware(['auth'])->group(function () {
    Route::get('/profile/notifications', [NotificationPreferencesController::class, 'edit'])->name('profile.notifications.edit');
    Route::put('/profile/notifications', [NotificationPreferencesController::class, 'update'])->name('profile.notifications.update');
});
