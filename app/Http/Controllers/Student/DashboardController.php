<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the comprehensive Student Portal Dashboard.
     */
    public function index(Request $request): View
    {
        /** @var User $student */
        $student = $request->user();

        // 1. Enrolled Classes with Teacher and Course
        $enrolledClasses = $student->enrolledClasses()
            ->with(['course', 'teacher'])
            ->get();

        $enrolledClassIds = $enrolledClasses->pluck('id');

        // 2. Attendance Summary
        $attendances = Attendance::where('student_id', $student->id)->get();
        $totalSessions = $attendances->count();
        $presentSessions = $attendances->where('status', 'Present')->count();
        $lateSessions = $attendances->where('status', 'Late')->count();
        $absentSessions = $attendances->where('status', 'Absent')->count();
        $attendanceRate = $totalSessions > 0 ? round(($presentSessions / $totalSessions) * 100, 1) : null;

        // Recent 5 attendance logs
        $recentAttendances = Attendance::where('student_id', $student->id)
            ->with('class.course')
            ->latest('date')
            ->take(5)
            ->get();

        // 3. Academic Evaluations & Grades
        $grades = Grade::where('student_id', $student->id)
            ->with('class.course')
            ->orderByDesc('assessment_date')
            ->get();

        $gradeAverage = $grades->isNotEmpty() ? round($grades->avg('percentage'), 1) : null;
        $recentGrades = $grades->take(4);

        // 4. Financial & Invoicing Summary
        $invoices = Invoice::where('student_id', $student->id)->get();
        $totalBilled = (float) $invoices->sum('total_amount');
        $totalPaid = 0.0;
        foreach ($invoices as $inv) {
            $totalPaid += $inv->totalPaid();
        }
        $totalBalanceDue = max(0.0, $totalBilled - $totalPaid);
        $unpaidInvoicesCount = $invoices->whereIn('status', ['Unpaid', 'Partial'])->count();

        // Recent Payments
        $recentPayments = Payment::whereHas('invoice', fn ($q) => $q->where('student_id', $student->id))
            ->with('invoice.items.course')
            ->latest('paid_at')
            ->take(4)
            ->get();

        // 5. Course Discovery & Wishlists
        $wishlistedCount = $student->wishlists()->count();
        $featuredCourses = Course::whereNotIn('id', function ($query) use ($enrolledClassIds) {
            $query->select('course_id')
                ->from('classes')
                ->whereIn('id', $enrolledClassIds);
        })
            ->latest()
            ->take(3)
            ->get();

        // 6. Linked Parents / Guardians
        $parents = $student->parents()->get();

        return view('student.dashboard', compact(
            'student',
            'enrolledClasses',
            'attendanceRate',
            'totalSessions',
            'presentSessions',
            'lateSessions',
            'absentSessions',
            'recentAttendances',
            'gradeAverage',
            'recentGrades',
            'totalBilled',
            'totalPaid',
            'totalBalanceDue',
            'unpaidInvoicesCount',
            'recentPayments',
            'wishlistedCount',
            'featuredCourses',
            'parents'
        ));
    }
}
