<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Admin dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_students' => User::whereHas('roles', fn ($q) => $q->where('name', 'student'))->count(),
            'total_teachers' => User::whereHas('roles', fn ($q) => $q->where('name', 'teacher'))->count(),
            'total_courses' => Course::count(),
            'active_classes' => ClassRoom::where('status', 'active')->count(),
            'total_revenue' => (float) Payment::sum('amount_paid'),
            'unpaid_invoices' => Invoice::where('status', '!=', 'Paid')->count(),
            'pending_amount' => (float) Invoice::where('status', '!=', 'Paid')->sum('total_amount') - (float) Payment::sum('amount_paid'),
        ];

        $recentEnrollments = CourseStudent::with(['class.course', 'student'])
            ->latest()
            ->take(5)
            ->get();

        $recentInvoices = Invoice::with(['student', 'creator'])
            ->latest()
            ->take(5)
            ->get();

        $recentPayments = Payment::with(['invoice.student', 'recordedBy'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentEnrollments', 'recentInvoices', 'recentPayments'));
    }
}
