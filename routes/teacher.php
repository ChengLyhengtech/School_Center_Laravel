<?php

use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\ClassScheduleController;
use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\GradeController;
use App\Http\Controllers\Teacher\ProfileController;
use App\Http\Controllers\Teacher\StudentRosterController;
use App\Models\ClassRoom;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Teacher Portal Routes
|--------------------------------------------------------------------------
|
| Prefix: /teacher
| Route Name Prefix: teacher.
| Middleware: web, auth, role:teacher (handled in bootstrap/app.php)
|
*/

// 1. Dashboard & Schedule Overview
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// 2. Classes & Schedules
Route::prefix('classes')->name('classes.')->group(function () {
    Route::get('/', [ClassScheduleController::class, 'index'])->name('index');
    Route::get('/{class}', [ClassScheduleController::class, 'show'])->name('show');
});

// Legacy Course Alias Routes
Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('/', [ClassScheduleController::class, 'index'])->name('index');
    Route::get('/{course}/attendance', function ($course) {
        $firstClass = ClassRoom::where('course_id', $course)
            ->where('teacher_id', auth()->id())
            ->first();

        return redirect()->route('teacher.attendance.create', ['class_id' => $firstClass?->id]);
    })->name('attendance.index');
});

// 3. Student Roster & Class Management
Route::prefix('roster')->name('roster.')->group(function () {
    Route::get('/', [StudentRosterController::class, 'index'])->name('index');
    Route::get('/classes/{class}/students/{student}', [StudentRosterController::class, 'show'])->name('show');
});

// 4. Attendance Tracking & Management
Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('index');
    Route::get('/record', [AttendanceController::class, 'create'])->name('create');
    Route::post('/record', [AttendanceController::class, 'store'])->name('store');
    Route::get('/history', [AttendanceController::class, 'history'])->name('history');
});

// 5. Grade & Score Management (edit-score permission)
Route::prefix('grades')->name('grades.')->group(function () {
    Route::get('/', [GradeController::class, 'index'])->name('index');
    Route::get('/create', [GradeController::class, 'create'])->name('create');
    Route::post('/', [GradeController::class, 'store'])->name('store');
    Route::get('/classes/{class}/assessments/{title}/edit', [GradeController::class, 'edit'])->name('edit');
    Route::put('/classes/{class}/assessments/{title}', [GradeController::class, 'update'])->name('update');
    Route::delete('/classes/{class}/assessments/{title}', [GradeController::class, 'destroy'])->name('destroy');
});

// 6. Teacher Profile & Account Settings
Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
});
