<?php

use App\Http\Controllers\ProfileController;
use App\Models\Course;
use App\Models\SchoolProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    if (auth()->check() && ! $request->has('preview') && ! $request->has('home')) {
        return redirect($request->user()->dashboardRoute());
    }

    $school = SchoolProfile::first();
    $featuredCourses = Course::with(['classes.teacher'])->latest()->take(6)->get();

    return view('welcome', compact('school', 'featuredCourses'));
})->name('home');

// General /dashboard redirector based on user role
Route::get('/dashboard', function (Request $request) {
    return redirect($request->user()->dashboardRoute());
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
