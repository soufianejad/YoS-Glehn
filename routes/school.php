<?php

use App\Http\Controllers\School\BookAssignmentController;
use App\Http\Controllers\School\ClassController;
use App\Http\Controllers\School\DashboardController as SchoolDashboardController;
use App\Http\Controllers\School\StudentController;
use Illuminate\Support\Facades\Route;

// Tableau de bord
Route::get('/', [SchoolDashboardController::class, 'index'])->name('dashboard');
Route::get('/qr-code', [SchoolDashboardController::class, 'showQrCode'])->name('qrcode');
Route::get('/statistics', [SchoolDashboardController::class, 'statistics'])->name('statistics');
Route::get('/progress-report', [SchoolDashboardController::class, 'progressReport'])->name('progress-report');

// Gestion des étudiants
Route::resource('students', StudentController::class)->names('students');
Route::post('/students/{student}/deactivate', [StudentController::class, 'deactivate'])->name('students.deactivate');
Route::post('/students/{student}/activate', [StudentController::class, 'activate'])->name('students.activate');
Route::get('/students/import/create', [StudentController::class, 'importCreate'])->name('students.import.create');
Route::get('/students/import/template', [StudentController::class, 'downloadTemplate'])->name('students.import.template');
Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
Route::get('/students/export', [StudentController::class, 'export'])->name('students.export');

// Gestion des classes
Route::resource('classes', ClassController::class)->names('classes');
Route::post('/classes/{class}/students', [ClassController::class, 'addStudents'])->name('classes.add-students');
Route::get('/classes/{class}/students/add', [ClassController::class, 'addStudentsForm'])->name('classes.add-students-form');
Route::delete('/classes/{class}/students/{student}', [ClassController::class, 'removeStudent'])->name('classes.remove-student');

// Attribution de livres
Route::resource('books/assignments', BookAssignmentController::class)->names('books.assignments');

// Annonces et communication
Route::resource('announcements', App\Http\Controllers\School\AnnouncementController::class)->names('announcements');

// Paramètres de l'école
Route::get('/settings', [SchoolDashboardController::class, 'settings'])->name('settings');
Route::put('/settings', [SchoolDashboardController::class, 'updateSettings'])->name('settings.update');
Route::post('/settings/regenerate-access-code', [SchoolDashboardController::class, 'regenerateAccessCode'])->name('settings.regenerate-access-code');

// Gestion des abonnements
Route::prefix('subscription')->name('subscription.')->group(function () {
    Route::get('/', [App\Http\Controllers\School\SubscriptionController::class, 'index'])->name('index');
    Route::get('/plans', [App\Http\Controllers\School\SubscriptionController::class, 'showPlans'])->name('plans');
    Route::post('/subscribe/{plan}', [App\Http\Controllers\School\SubscriptionController::class, 'subscribe'])->name('subscribe');
});

// Gestion des enseignants
Route::resource('teachers', App\Http\Controllers\School\TeacherController::class)->names('teachers');

// Gestion des parents
Route::resource('parents', App\Http\Controllers\School\ParentController::class)->names('parents');
