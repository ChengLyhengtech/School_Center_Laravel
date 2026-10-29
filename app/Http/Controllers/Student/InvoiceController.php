<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Display all invoices, line-item summaries, and payment history.
     */
    public function index(Request $request): View
    {
        /** @var User $student */
        $student = $request->user();
        $status = $request->query('status', 'all');

        $query = Invoice::where('student_id', $student->id)
            ->with(['items.course', 'discount', 'payments']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $invoices = $query->latest()->get();

        // Financial totals
        $allInvoices = Invoice::where('student_id', $student->id)->with('payments')->get();
        $totalBilled = (float) $allInvoices->sum('total_amount');
        $totalPaid = 0.0;
        foreach ($allInvoices as $inv) {
            $totalPaid += $inv->totalPaid();
        }
        $totalBalanceDue = max(0.0, $totalBilled - $totalPaid);

        // Payment receipt history
        $payments = Payment::whereHas('invoice', fn ($q) => $q->where('student_id', $student->id))
            ->with(['invoice', 'recordedBy'])
            ->latest('paid_at')
            ->get();

        return view('student.invoices.index', compact(
            'invoices',
            'payments',
            'totalBilled',
            'totalPaid',
            'totalBalanceDue',
            'status'
        ));
    }

    /**
     * Display a specific invoice with full itemized line breakdown and receipt history.
     */
    public function show(Request $request, Invoice $invoice): View
    {
        /** @var User $student */
        $student = $request->user();

        // Ensure this invoice belongs to the student
        abort_unless($invoice->student_id === $student->id, 403, 'Unauthorized access to invoice.');

        $invoice->load(['items.course', 'discount', 'payments.recordedBy', 'student']);
        $school = SchoolProfile::first();

        return view('student.invoices.show', compact('invoice', 'school'));
    }
}
