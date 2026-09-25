<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::view('/attendance/scan', 'attendance.scan')->name('attendance.scan');
    Route::get('/attendance/history', [\App\Http\Controllers\Attendance\AttendanceController::class, 'index'])->name('attendance.history');
    Route::get('/reports', [\App\Http\Controllers\Supervision\ReportController::class, 'index'])->name('reports.index');
    Route::view('/settings', 'settings.index')->name('settings.index');
    Route::view('/supervision', 'supervision.index')->name('supervision.dashboard');
    Route::view('/admin', 'admin.index')->name('admin.dashboard');
    Route::view('/absences/create', 'absences.create')->name('absences.create');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Attendance\AttendanceController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Attendance\AttendanceController::class, 'store'])->name('store');
        Route::post('/qr-tokens', [\App\Http\Controllers\Attendance\QrTokenController::class, 'store'])->name('qr-tokens.store');
    });

    Route::prefix('absences')->name('absences.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Absence\JustificatifController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Absence\JustificatifController::class, 'store'])->name('store');
        Route::get('/{justificatif}', [\App\Http\Controllers\Absence\JustificatifController::class, 'show'])->name('show');
        Route::patch('/{justificatif}/review', [\App\Http\Controllers\Absence\JustificatifController::class, 'review'])->name('review');
    });

    Route::post('/reports', [\App\Http\Controllers\Supervision\ReportController::class, 'store'])->name('reports.store');
    Route::patch('/reports/{rapport}/validate', [\App\Http\Controllers\Supervision\ReportController::class, 'validateReport'])->name('reports.validate');
    Route::get('/supervision/personnel', [\App\Http\Controllers\Supervision\PersonnelController::class, 'index'])->name('supervision.personnel');
    Route::get('/supervision/attendance', [\App\Http\Controllers\Supervision\PersonnelController::class, 'attendance'])->name('supervision.attendance');
    Route::get('/admin/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('admin.users.edit');
    Route::patch('/admin/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
});

require __DIR__.'/auth.php';
