<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display a listing of courses.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = Course::withCount(['classes', 'wishlists', 'discounts'])->latest();

        if ($search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $courses = $query->paginate(10)->withQueryString();

        return view('admin.courses.index', compact('courses', 'search'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): View
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $course = Course::create($validated);

        return redirect()->route('admin.courses.index')->with('success', "Course '{$course->title}' created successfully.");
    }

    /**
     * Display the specified course and its scheduled classes.
     */
    public function show(Course $course): View
    {
        $course->load([
            'classes.teacher',
            'classes.students',
            'discounts',
            'invoiceItems.invoice',
        ]);

        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course): View
    {
        return view('admin.courses.edit', compact('course'));
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $course->update($validated);

        return redirect()->route('admin.courses.show', $course)->with('success', "Course '{$course->title}' updated successfully.");
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $course): RedirectResponse
    {
        $courseTitle = $course->title;
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', "Course '{$courseTitle}' deleted successfully.");
    }
}
