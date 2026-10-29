<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Display personal attendance logs and consistency reports.
     */
    public function index(Request $request): View
    {
        /** @var User $student */
        $student = $request->user();
        $classId = $request->query('class_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $enrolledClasses = $student->enrolledClasses()->with('course')->get();

        $query = Attendance::where('student_id', $student->id)
            ->with(['class.course', 'class.teacher']);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        $attendances = $query->orderByDesc('date')->get();

        // Calculate aggregate statistics
        $totalSessions = $attendances->count();
        $presentCount = $attendances->where('status', 'Present')->count();
        $lateCount = $attendances->where('status', 'Late')->count();
        $absentCount = $attendances->where('status', 'Absent')->count();
        $attendanceRate = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100, 1) : null;

        // Breakdown per enrolled class
        $classBreakdown = $enrolledClasses->map(function ($cls) use ($student) {
            $records = Attendance::where('class_id', $cls->id)
                ->where('student_id', $student->id)
                ->get();

            $total = $records->count();
            $present = $records->where('status', 'Present')->count();
            $late = $records->where('status', 'Late')->count();
            $absent = $records->where('status', 'Absent')->count();
            $rate = $total > 0 ? round(($present / $total) * 100, 1) : null;

            return (object) [
                'class' => $cls,
                'total' => $total,
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
                'rate' => $rate,
            ];
        });

        return view('student.attendance.index', compact(
            'enrolledClasses',
            'attendances',
            'totalSessions',
            'presentCount',
            'lateCount',
            'absentCount',
            'attendanceRate',
            'classBreakdown',
            'classId',
            'startDate',
            'endDate'
        ));
    }
}
