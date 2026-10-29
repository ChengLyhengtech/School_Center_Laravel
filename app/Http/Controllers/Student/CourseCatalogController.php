<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseCatalogController extends Controller
{
    /**
     * Browse available courses with pricing, duration, and wishlist status.
     */
    public function index(Request $request): View
    {
        /** @var User $student */
        $student = $request->user();
        $search = $request->query('search');
        $tab = $request->query('tab', 'all'); // 'all' or 'wishlist'

        $wishlistedCourseIds = $student->wishlists()->pluck('course_id')->toArray();
        $enrolledCourseIds = $student->enrolledCourses()->pluck('id')->toArray();

        $query = Course::with(['classes.teacher']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($tab === 'wishlist') {
            $query->whereIn('id', $wishlistedCourseIds);
        }

        $courses = $query->orderBy('title')->get();

        // Calculate wishlist total tuition estimate for walk-in enrollment
        $wishlistTotalTuition = Course::whereIn('id', $wishlistedCourseIds)->sum('price');

        return view('student.catalog.index', compact(
            'courses',
            'wishlistedCourseIds',
            'enrolledCourseIds',
            'wishlistTotalTuition',
            'search',
            'tab'
        ));
    }

    /**
     * Display comprehensive details for a course.
     */
    public function show(Request $request, Course $course): View
    {
        /** @var User $student */
        $student = $request->user();

        $course->load(['classes.teacher']);
        $isWishlisted = $student->wishlists()->where('course_id', $course->id)->exists();
        $isEnrolled = $student->enrolledCourses()->where('courses.id', $course->id)->exists();

        return view('student.catalog.show', compact('course', 'isWishlisted', 'isEnrolled'));
    }

    /**
     * Toggle a course in the student's personal wishlist.
     */
    public function toggleWishlist(Request $request, Course $course): RedirectResponse
    {
        /** @var User $student */
        $student = $request->user();

        $existing = Wishlist::where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = "Removed '{$course->title}' from your wishlist.";
        } else {
            Wishlist::create([
                'user_id' => $student->id,
                'course_id' => $course->id,
            ]);
            $message = "Added '{$course->title}' to your wishlist for walk-in registration!";
        }

        return back()->with('success', $message);
    }
}
