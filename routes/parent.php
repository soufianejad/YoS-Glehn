<?php

use App\Http\Controllers\Parent\DashboardController;
use Illuminate\Support\Facades\Route;

// Parent Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/child/{child}', [DashboardController::class, 'showChild'])->name('child.show');
