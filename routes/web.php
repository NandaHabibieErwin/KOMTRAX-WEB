<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnitTrackerController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/upload-excel', [UnitTrackerController::class, 'UploadExcelData'])->name('UploadExcel');
    Route::get('/get-excel', [UnitTrackerController::class, 'ReadMainExcel'])->name('ReadMainExcel');
    Route::get('/enroll', [UnitTrackerController::class, 'index'])->name('enroll');
    Route::get('/upload', [AdminController::class, 'index'])->name('upload');
    Route::get('/delete-duplicate', [UnitTrackerController::class, 'DeleteDuplicateData'])->name('DeleteDuplicate');
});

require __DIR__.'/auth.php';
