<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassScheduleController extends Controller
{
    /**
     * Display the student's enrolled classes and weekly timetable hub.
     */
    public function index(Request $request): View
    {
        /** @var User $student */
        $student = $request->user();
        $status = $request->query('status', 'all');

        $query = $student->enrolledClasses()->with(['course', 'teacher']);

        if ($status !== 'all') {
            $query->where('classes.status', $status);
        }

        $enrolledClasses = $query->orderBy('name')->get();

        // Calculate attendance rate and grade average per class
        foreach ($enrolledClasses as $class) {
            $totalAttendances = Attendance::where('class_id', $class->id)
                ->where('student_id', $student->id)
                ->count();

            $presentAttendances = Attendance::where('class_id', $class->id)
                ->where('student_id', $student->id)
                ->where('status', 'Present')
                ->count();

            $class->attendance_rate = $totalAttendances > 0
                ? round(($presentAttendances / $totalAttendances) * 100, 1)
                : null;

            $classGrades = Grade::where('class_id', $class->id)
                ->where('student_id', $student->id)
                ->get();

            $class->grade_average = $classGrades->isNotEmpty()
                ? round($classGrades->avg('percentage'), 1)
                : null;
        }

        return view('student.classes.index', compact('enrolledClasses', 'status'));
    }

    /**
     * Display comprehensive details, syllabus outline, and instructor contact for a class.
     */
    public function show(Request $request, ClassRoom $class): View
    {
        /** @var User $student */
        $student = $request->user();

        // Ensure student is actually enrolled in this class
        $isEnrolled = $class->students()->where('student_id', $student->id)->exists();
        abort_unless($isEnrolled, 403, 'You are not enrolled in this class section.');

        $class->load(['course', 'teacher']);

        // Student's personal attendance in this class
        $attendances = Attendance::where('class_id', $class->id)
            ->where('student_id', $student->id)
            ->orderByDesc('date')
            ->get();

        $totalSessions = $attendances->count();
        $presentSessions = $attendances->where('status', 'Present')->count();
        $lateSessions = $attendances->where('status', 'Late')->count();
        $absentSessions = $attendances->where('status', 'Absent')->count();
        $attendanceRate = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100, 1) : null;

        // Student's personal grades in this class
        $grades = Grade::where('class_id', $class->id)
            ->where('student_id', $student->id)
            ->orderByDesc('assessment_date')
            ->get();

        $gradeAverage = $grades->isNotEmpty() ? round($grades->avg('percentage'), 1) : null;

        // Classmates count
        $classmatesCount = $class->students()->count();

        return view('student.classes.show', compact(
            'class',
            'attendances',
            'attendanceRate',
            'totalSessions',
            'presentSessions',
            'lateSessions',
            'absentSessions',
            'grades',
            'gradeAverage',
            'classmatesCount'
        ));
    }
}
