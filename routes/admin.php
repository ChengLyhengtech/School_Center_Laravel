<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ClassRoomController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\SchoolProfileController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Portal Routes
|--------------------------------------------------------------------------
|
| Prefix: /admin
| Route Name Prefix: admin.
| Middleware: web, auth, role:admin
|
*/

// Admin Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// 1. School Profile & System Configuration
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/profile', [SchoolProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [SchoolProfileController::class, 'update'])->name('profile.update');
});

// 2. User & RBAC Management
Route::prefix('users')->name('users.')->group(function () {
    Route::post('/{user}/link-child', [UserController::class, 'linkChild'])->name('link-child');
    Route::delete('/{user}/unlink-child/{child}', [UserController::class, 'unlinkChild'])->name('unlink-child');
});
Route::resource('users', UserController::class);

Route::prefix('roles-permissions')->name('roles-permissions.')->group(function () {
    Route::get('/', [RolePermissionController::class, 'index'])->name('index');
    Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
    Route::post('/permissions', [RolePermissionController::class, 'storePermission'])->name('permissions.store');
    Route::put('/roles/{role}/permissions', [RolePermissionController::class, 'updateRolePermissions'])->name('roles.update-permissions');
});

// 3. Academic & Curriculum Management
Route::resource('courses', CourseController::class);
Route::resource('classes', ClassRoomController::class);

// 4. Enrollment & Attendance Tracking
Route::prefix('enrollments')->name('enrollments.')->group(function () {
    Route::get('/', [EnrollmentController::class, 'index'])->name('index');
    Route::post('/', [EnrollmentController::class, 'store'])->name('store');
    Route::delete('/{enrollment}', [EnrollmentController::class, 'destroy'])->name('destroy');
});

Route::prefix('attendances')->name('attendances.')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('index');
    Route::post('/', [AttendanceController::class, 'store'])->name('store');
});

// 5. Financial & Invoicing Operations
Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
Route::resource('invoices', InvoiceController::class)->only(['index', 'create', 'store', 'show']);

Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::get('/create', [PaymentController::class, 'create'])->name('create');
    Route::post('/', [PaymentController::class, 'store'])->name('store');
});

Route::resource('discounts', DiscountController::class)->except(['show']);
