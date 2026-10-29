<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentRosterController extends Controller
{
    /**
     * Display the student roster for the teacher's assigned classes.
     */
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $classId = $request->query('class_id');
        $search = $request->query('search');

        // Get all classes taught by this teacher for filtering
        $myClasses = ClassRoom::where('teacher_id', $teacher->id)
            ->with('course')
            ->orderBy('name')
            ->get();

        $myClassIds = $myClasses->pluck('id');

        $query = ClassRoom::whereIn('id', $myClassIds)
            ->with([
                'course',
                'students' => function ($q) use ($search) {
                    if ($search) {
                        $q->where(function ($sub) use ($search) {
                            $sub->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                    }
                    $q->with('parents')->orderBy('name');
                },
            ]);

        if ($classId && $myClassIds->contains((int) $classId)) {
            $query->where('id', $classId);
        }

        $classesWithRosters = $query->get();

        return view('teacher.roster.index', compact('myClasses', 'classesWithRosters', 'classId', 'search'));
    }

    /**
     * Display a specific student's performance card and emergency contacts within a class.
     */
    public function show(Request $request, ClassRoom $class, User $student): View
    {
        // Authorization: class must belong to teacher
        abort_unless($class->teacher_id === $request->user()->id, 403, 'Unauthorized class section.');

        // Student must be enrolled in this class
        abort_unless($class->students()->where('users.id', $student->id)->exists(), 404, 'Student is not enrolled in this class.');

        $student->load('parents');

        // Student's attendance records in this class
        $attendances = Attendance::where('class_id', $class->id)
            ->where('student_id', $student->id)
            ->orderByDesc('date')
            ->get();

        $totalSessions = $attendances->count();
        $presentCount = $attendances->where('status', 'Present')->count();
        $lateCount = $attendances->where('status', 'Late')->count();
        $absentCount = $attendances->where('status', 'Absent')->count();
        $attendanceRate = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100, 1) : null;

        // Student's grades in this class
        $grades = Grade::where('class_id', $class->id)
            ->where('student_id', $student->id)
            ->orderByDesc('assessment_date')
            ->get();

        $averageScore = $grades->isNotEmpty() ? round($grades->avg('percentage'), 1) : null;

        return view('teacher.roster.show', compact(
            'class',
            'student',
            'attendances',
            'totalSessions',
            'presentCount',
            'lateCount',
            'absentCount',
            'attendanceRate',
            'grades',
            'averageScore'
        ));
    }
}
