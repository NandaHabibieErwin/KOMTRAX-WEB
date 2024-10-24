<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EnrollController;
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

// Web Route
Route::middleware('auth')->group(function () {
    Route::get('/get-excel', [UnitTrackerController::class, 'ReadMainExcel'])->name('ReadMainExcel');
    Route::get('/enroll', [UnitTrackerController::class, 'index'])->name('enroll');

    // Filter Management
    Route::post('/save-filter', [EnrollController::class, 'AddEnroll'])->name("save-filter");
    Route::put('/update-filter', [EnrollController::class, 'EditEnroll'])->name("update-filter");
    Route::get('/read-enroll', [EnrollController::class, 'ReadEnroll'])->name('read-enroll');
    Route::delete('/delete-filter', [EnrollController::class, 'DeleteEnroll'])->name('delete-filter');
});


//Data Management Route
Route::middleware(['auth', 'CheckRole:admin'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/upload-excel', [UnitTrackerController::class, 'UploadExcelData'])->name('UploadExcel');
    Route::get('/upload', [AdminController::class, 'index'])->name('upload');
    Route::get('/getcustomername', [AdminController::class, 'RetrieveCustomerName'])->name('GetCustomerName');
});

// User Status Check Route
Route::get('/user-status', function (Request $request) {
    return response()->json([
        'user' => auth()->user(),
    ]);
});
require __DIR__.'/auth.php';
