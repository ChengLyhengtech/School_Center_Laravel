<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\CourseStudent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of enrollments across classes.
     */
    public function index(Request $request): View
    {
        $classFilter = $request->query('class_id');
        $search = $request->query('search');

        $query = CourseStudent::with(['class.course', 'class.teacher', 'student'])->latest();

        if ($classFilter) {
            $query->where('class_id', $classFilter);
        }

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $enrollments = $query->paginate(15)->withQueryString();
        $classes = ClassRoom::with('course')->where('status', 'active')->get();
        $students = User::whereHas('roles', fn ($q) => $q->where('name', 'student'))->get();

        return view('admin.enrollments.index', compact('enrollments', 'classes', 'students', 'classFilter', 'search'));
    }

    /**
     * Enroll a student into a class.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'student_id' => ['required', 'exists:users,id'],
            'enrolled_date' => ['required', 'date'],
        ]);

        $exists = CourseStudent::where('class_id', $validated['class_id'])
            ->where('student_id', $validated['student_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'This student is already enrolled in the selected class.');
        }

        CourseStudent::create($validated);

        return back()->with('success', 'Student enrolled into class successfully.');
    }

    /**
     * Unenroll a student from a class.
     */
    public function destroy(CourseStudent $enrollment): RedirectResponse
    {
        $enrollment->delete();

        return back()->with('success', 'Student unenrolled from class.');
    }
}
