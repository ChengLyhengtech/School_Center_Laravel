<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassRoomController extends Controller
{
    /**
     * Display a listing of classes / batches.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');
        $courseFilter = $request->query('course_id');
        $search = $request->query('search');

        $query = ClassRoom::with(['course', 'teacher'])->withCount('students')->latest();

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if ($courseFilter) {
            $query->where('course_id', $courseFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('room', 'like', "%{$search}%");
            });
        }

        $classes = $query->paginate(12)->withQueryString();
        $courses = Course::all();
        $teachers = User::whereHas('roles', fn ($q) => $q->where('name', 'teacher'))->get();

        return view('admin.classes.index', compact('classes', 'courses', 'teachers', 'statusFilter', 'courseFilter', 'search'));
    }

    /**
     * Show the form for creating a new class batch.
     */
    public function create(): View
    {
        $courses = Course::all();
        $teachers = User::whereHas('roles', fn ($q) => $q->where('name', 'teacher'))->get();

        return view('admin.classes.create', compact('courses', 'teachers'));
    }

    /**
     * Store a newly created class in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'room' => ['nullable', 'string', 'max:100'],
            'days' => ['nullable', 'string', 'max:100'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'status' => ['required', 'in:active,completed,cancelled'],
        ]);

        $class = ClassRoom::create($validated);

        return redirect()->route('admin.classes.index')->with('success', "Class '{$class->name}' created successfully.");
    }

    /**
     * Display the specified class and its student roster.
     */
    public function show(ClassRoom $class): View
    {
        $class->load([
            'course',
            'teacher',
            'students',
            'attendances.student',
        ]);

        $availableStudents = User::whereHas('roles', fn ($q) => $q->where('name', 'student'))
            ->whereNotIn('id', $class->students->pluck('id'))
            ->get();

        return view('admin.classes.show', compact('class', 'availableStudents'));
    }

    /**
     * Show the form for editing the specified class.
     */
    public function edit(ClassRoom $class): View
    {
        $courses = Course::all();
        $teachers = User::whereHas('roles', fn ($q) => $q->where('name', 'teacher'))->get();

        return view('admin.classes.edit', compact('class', 'courses', 'teachers'));
    }

    /**
     * Update the specified class in storage.
     */
    public function update(Request $request, ClassRoom $class): RedirectResponse
    {
        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'room' => ['nullable', 'string', 'max:100'],
            'days' => ['nullable', 'string', 'max:100'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'status' => ['required', 'in:active,completed,cancelled'],
        ]);

        $class->update($validated);

        return redirect()->route('admin.classes.show', $class)->with('success', "Class '{$class->name}' updated successfully.");
    }

    /**
     * Remove the specified class from storage.
     */
    public function destroy(ClassRoom $class): RedirectResponse
    {
        $className = $class->name;
        $class->delete();

        return redirect()->route('admin.classes.index')->with('success', "Class '{$className}' deleted successfully.");
    }
}
