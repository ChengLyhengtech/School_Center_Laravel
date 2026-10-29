<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassRoom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Display classes attendance hub.
     */
    public function index(Request $request): View
    {
        $teacher = $request->user();

        $classes = ClassRoom::where('teacher_id', $teacher->id)
            ->with(['course', 'students'])
            ->withCount(['students', 'attendances'])
            ->orderBy('name')
            ->get();

        // Calculate attendance rate for each class
        foreach ($classes as $class) {
            $total = $class->attendances_count;
            $present = Attendance::where('class_id', $class->id)->where('status', 'Present')->count();
            $class->attendance_rate = $total > 0 ? round(($present / $total) * 100, 1) : null;
            $class->last_attendance_date = Attendance::where('class_id', $class->id)->max('date');
        }

        return view('teacher.attendance.index', compact('classes'));
    }

    /**
     * Display the daily attendance marking form for a specific class & date.
     */
    public function create(Request $request): View
    {
        $teacher = $request->user();
        $classId = $request->query('class_id');
        $date = $request->query('date', now()->format('Y-m-d'));

        $myClasses = ClassRoom::where('teacher_id', $teacher->id)
            ->with('course')
            ->orderBy('name')
            ->get();

        $selectedClass = null;
        $students = collect();
        $existingRecords = collect();

        if ($classId) {
            $selectedClass = $myClasses->firstWhere('id', (int) $classId);
            abort_unless($selectedClass, 403, 'Unauthorized class section.');

            $students = $selectedClass->students()->orderBy('name')->get();

            // Load existing attendance for this date (if already marked)
            $existingRecords = Attendance::where('class_id', $selectedClass->id)
                ->whereDate('date', $date)
                ->get()
                ->keyBy('student_id');
        }

        return view('teacher.attendance.record', compact('myClasses', 'selectedClass', 'date', 'students', 'existingRecords'));
    }

    /**
     * Store or update attendance records in bulk.
     */
    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user();

        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'date' => ['required', 'date'],
            'attendances' => ['required', 'array'],
            'attendances.*.student_id' => ['required', 'exists:users,id'],
            'attendances.*.status' => ['required', 'in:Present,Absent,Late'],
        ]);

        $class = ClassRoom::where('id', $validated['class_id'])
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        foreach ($validated['attendances'] as $record) {
            Attendance::updateOrCreate(
                [
                    'class_id' => $class->id,
                    'student_id' => $record['student_id'],
                    'date' => $validated['date'],
                ],
                [
                    'status' => $record['status'],
                ]
            );
        }

        return redirect()->route('teacher.attendance.history', ['class_id' => $class->id])
            ->with('success', "Attendance for {$class->name} on {$validated['date']} was successfully recorded.");
    }

    /**
     * Display attendance history and truancy trends for a class.
     */
    public function history(Request $request): View
    {
        $teacher = $request->user();
        $classId = $request->query('class_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $myClasses = ClassRoom::where('teacher_id', $teacher->id)
            ->with('course')
            ->orderBy('name')
            ->get();

        $selectedClass = null;
        $attendanceDates = collect();
        $studentAttendanceSummaries = collect();

        if ($classId) {
            $selectedClass = $myClasses->firstWhere('id', (int) $classId);
            abort_unless($selectedClass, 403, 'Unauthorized class section.');

            $query = Attendance::where('class_id', $selectedClass->id);

            if ($startDate) {
                $query->whereDate('date', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('date', '<=', $endDate);
            }

            $attendances = $query->with('student')->orderByDesc('date')->get();

            // Distinct dates recorded
            $attendanceDates = $attendances->pluck('date')->unique()->values();

            // Calculate per-student stats and truancy indicators
            $enrolledStudents = $selectedClass->students()->orderBy('name')->get();

            $studentAttendanceSummaries = $enrolledStudents->map(function ($student) use ($attendances) {
                $records = $attendances->where('student_id', $student->id);
                $total = $records->count();
                $present = $records->where('status', 'Present')->count();
                $late = $records->where('status', 'Late')->count();
                $absent = $records->where('status', 'Absent')->count();
                $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;
                $isTruant = $absent >= 3; // Truancy alert if 3 or more absences

                return (object) [
                    'student' => $student,
                    'total' => $total,
                    'present' => $present,
                    'late' => $late,
                    'absent' => $absent,
                    'rate' => $rate,
                    'is_truant' => $isTruant,
                ];
            });
        }

        return view('teacher.attendance.history', compact(
            'myClasses',
            'selectedClass',
            'attendanceDates',
            'studentAttendanceSummaries',
            'classId',
            'startDate',
            'endDate'
        ));
    }
}
