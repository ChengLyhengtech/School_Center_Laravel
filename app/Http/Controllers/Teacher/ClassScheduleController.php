<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassScheduleController extends Controller
{
    /**
     * Display a list of classes assigned to the authenticated teacher.
     */
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $status = $request->query('status');
        $search = $request->query('search');

        $query = ClassRoom::where('teacher_id', $teacher->id)
            ->with(['course'])
            ->withCount(['students', 'attendances', 'grades']);

        if ($status && in_array($status, ['active', 'upcoming', 'completed', 'cancelled'])) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('room', 'like', "%{$search}%")
                    ->orWhereHas('course', fn ($c) => $c->where('title', 'like', "%{$search}%"));
            });
        }

        $classes = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('teacher.classes.index', compact('classes', 'status', 'search'));
    }

    /**
     * Display comprehensive details and syllabus for an assigned class.
     */
    public function show(Request $request, ClassRoom $class): View
    {
        // Authorization: ensure this class belongs to the logged-in teacher
        abort_unless($class->teacher_id === $request->user()->id, 403, 'Unauthorized access to this class section.');

        $class->load([
            'course',
            'students' => fn ($q) => $q->orderBy('name'),
            'students.parents',
            'grades' => fn ($q) => $q->latest('assessment_date')->take(5),
            'attendances' => fn ($q) => $q->latest('date')->take(10),
        ]);

        $enrolledCount = $class->students->count();
        $totalAttendanceCount = $class->attendances->count();
        $presentCount = $class->attendances->where('status', 'Present')->count();
        $attendanceRate = $totalAttendanceCount > 0 ? round(($presentCount / $totalAttendanceCount) * 100, 1) : null;

        return view('teacher.classes.show', compact('class', 'enrolledCount', 'attendanceRate'));
    }
}
