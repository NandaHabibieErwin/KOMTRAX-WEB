<?php

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

Route::get('/enroll', function () {
    return Inertia::render('Enroll');
})->middleware(['auth', 'verified'])->name('enroll');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/upload-excel', [UnitTrackerController::class, 'UploadExcelData'])->name('UploadExcel');
    Route::post('/read-excel', [UnitTrackerController::class, 'ReadExcelData'])->name('ReadExcel');
    Route::get('/get-excel/{id}', [UnitTrackerController::class, 'GetExcelFile']);
    Route::get('/download-excel/{id}', [UnitTrackerController::class, 'downloadExcelFile']);
    Route::get('/retrieve', [UnitTrackerController::class, 'ReadExcelData']);
    Route::get('/get-excel-all', [UnitTrackerController::class, 'getAllExcelFiles']);

});

require __DIR__.'/auth.php';
