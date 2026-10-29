<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Display a listing of all payment transactions.
     */
    public function index(Request $request): View
    {
        $methodFilter = $request->query('payment_method');
        $search = $request->query('search');

        $query = Payment::with(['invoice.student', 'recordedBy'])->latest();

        if ($methodFilter) {
            $query->where('payment_method', $methodFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('invoice', fn ($iq) => $iq->where('invoice_number', 'like', "%{$search}%"))
                    ->orWhereHas('invoice.student', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $payments = $query->paginate(15)->withQueryString();
        $totalCollected = (float) Payment::sum('amount_paid');

        return view('admin.payments.index', compact('payments', 'methodFilter', 'search', 'totalCollected'));
    }

    /**
     * Show form to record payment against an invoice.
     */
    public function create(Request $request): View
    {
        $invoiceId = $request->query('invoice_id');
        $invoices = Invoice::with('student')->where('status', '!=', 'Paid')->get();
        $selectedInvoice = $invoiceId ? Invoice::with(['student', 'payments'])->find($invoiceId) : null;

        return view('admin.payments.create', compact('invoices', 'selectedInvoice'));
    }

    /**
     * Record a payment transaction against an invoice.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_id' => ['required', 'exists:invoices,id'],
            'amount_paid' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'max:50'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'paid_at' => ['required', 'date'],
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);
        $remaining = $invoice->remainingBalance();

        if ((float) $validated['amount_paid'] > $remaining + 0.001) {
            return back()->with('error', "Amount exceeds remaining invoice balance of \${$remaining}.");
        }

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount_paid' => $validated['amount_paid'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'recorded_by' => $request->user()->id,
            'paid_at' => $validated['paid_at'],
        ]);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', "Payment of \${$payment->amount_paid} recorded successfully. Invoice status: {$invoice->fresh()->status}.");
    }
}
