<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceExportController;

Route::get('/', function () {
    return view('welcome');
});

// Export attendances (CSV fallback if Laravel-Excel not installed)
Route::get('attendances/export', [AttendanceExportController::class, 'export'])
    ->middleware(['auth'])
    ->name('attendances.export');
