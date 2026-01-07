<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceExportController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

// Export attendances (CSV fallback if Laravel-Excel not installed)
Route::get('attendances/export', [AttendanceExportController::class, 'export'])
    ->middleware(['auth'])
    ->name('attendances.export');

// Profile routes
Route::middleware('auth')->group(function () {
    // For users to manage their own profile
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    // Admins can edit other users' profiles
    Route::get('users/{user}/profile/edit', [ProfileController::class, 'edit'])->name('users.profile.edit');
    Route::put('users/{user}/profile', [ProfileController::class, 'update'])->name('users.profile.update');
});


