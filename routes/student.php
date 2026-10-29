<?php

use App\Http\Controllers\Student\AttendanceController;
use App\Http\Controllers\Student\ClassScheduleController;
use App\Http\Controllers\Student\CourseCatalogController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\GradebookController;
use App\Http\Controllers\Student\InvoiceController;
use App\Http\Controllers\Student\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Student Portal Routes
|--------------------------------------------------------------------------
|
| Prefix: /student
| Route Name Prefix: student.
| Middleware: web, auth, role:student (registered in bootstrap/app.php)
|
*/

// 1. Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// 2. Enrolled Classes & Schedule Hub
Route::prefix('classes')->name('classes.')->group(function () {
    Route::get('/', [ClassScheduleController::class, 'index'])->name('index');
    Route::get('/{class}', [ClassScheduleController::class, 'show'])->name('show');
});

// 3. Attendance Tracking & Summary
Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('index');
});
// Legacy attendance alias route
Route::get('/attendance-log', fn () => redirect()->route('student.attendance.index'))->name('attendance');

// 4. Gradebook & Academic Evaluations
Route::prefix('grades')->name('grades.')->group(function () {
    Route::get('/', [GradebookController::class, 'index'])->name('index');
});

// 5. Invoices & Payment Receipts
Route::prefix('invoices')->name('invoices.')->group(function () {
    Route::get('/', [InvoiceController::class, 'index'])->name('index');
    Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
});
// Legacy payments alias route
Route::get('/payments', fn () => redirect()->route('student.invoices.index'))->name('payments');

// 6. Course Discovery & Wishlist
Route::prefix('catalog')->name('catalog.')->group(function () {
    Route::get('/', [CourseCatalogController::class, 'index'])->name('index');
    Route::get('/courses/{course}', [CourseCatalogController::class, 'show'])->name('show');
    Route::post('/courses/{course}/wishlist', [CourseCatalogController::class, 'toggleWishlist'])->name('wishlist.toggle');
});
// Legacy courses alias routes
Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('/', fn () => redirect()->route('student.catalog.index'))->name('index');
    Route::get('/{course}', fn ($course) => redirect()->route('student.catalog.show', $course))->name('show');
});

// 7. Profile & Parent Connections
Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
});
