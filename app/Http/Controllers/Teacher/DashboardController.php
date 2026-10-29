<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Teacher Portal Dashboard overview.
     */
    public function index(Request $request): View
    {
        /** @var User $teacher */
        $teacher = $request->user();

        // 1. Classes assigned to this teacher
        $classes = ClassRoom::where('teacher_id', $teacher->id)
            ->with(['course', 'students'])
            ->withCount('students')
            ->orderBy('name')
            ->get();

        $activeClassesCount = $classes->where('status', 'active')->count();
        $totalStudentsCount = $classes->sum('students_count');

        // 2. Today's attendance stats for teacher's classes
        $today = now()->format('Y-m-d');
        $classIds = $classes->pluck('id');

        $todayAttendances = Attendance::whereIn('class_id', $classIds)
            ->whereDate('date', $today)
            ->get();

        $todayPresentCount = $todayAttendances->where('status', 'Present')->count();
        $todayAttendanceRate = $todayAttendances->isNotEmpty()
            ? round(($todayPresentCount / $todayAttendances->count()) * 100, 1)
            : null;

        // 3. Recent Attendance logs
        $recentAttendances = Attendance::whereIn('class_id', $classIds)
            ->with(['student', 'class.course'])
            ->latest('date')
            ->latest('id')
            ->take(6)
            ->get();

        // 4. Recent Grades submitted by this teacher
        $recentGrades = Grade::where('created_by', $teacher->id)
            ->with(['student', 'class.course'])
            ->latest('assessment_date')
            ->latest('id')
            ->take(5)
            ->get();

        return view('teacher.dashboard', compact(
            'teacher',
            'classes',
            'activeClassesCount',
            'totalStudentsCount',
            'todayAttendances',
            'todayAttendanceRate',
            'recentAttendances',
            'recentGrades'
        ));
    }
}
