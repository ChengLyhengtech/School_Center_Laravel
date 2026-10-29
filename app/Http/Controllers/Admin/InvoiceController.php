<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Discount;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');
        $search = $request->query('search');

        $query = Invoice::with(['student', 'discount', 'creator', 'payments'])->latest();

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('student', fn ($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $invoices = $query->paginate(12)->withQueryString();

        return view('admin.invoices.index', compact('invoices', 'statusFilter', 'search'));
    }

    /**
     * Show the form for creating a new walk-in invoice.
     */
    public function create(): View
    {
        $students = User::whereHas('roles', fn ($q) => $q->where('name', 'student'))->get();
        $courses = Course::all();
        $discounts = Discount::where('is_active', true)->get();

        return view('admin.invoices.create', compact('students', 'courses', 'discounts'));
    }

    /**
     * Store a newly created invoice with line items in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'discount_id' => ['nullable', 'exists:discounts,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.course_id' => ['nullable', 'exists:courses,id'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $itemTotal = (float) $item['unit_price'] * (int) $item['quantity'];
                $subtotal += $itemTotal;
                $itemsData[] = [
                    'course_id' => $item['course_id'] ?? null,
                    'description' => $item['description'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'total_price' => $itemTotal,
                ];
            }

            // Calculate discount
            $discountAmount = 0.00;
            $discountId = null;

            if (! empty($validated['discount_id'])) {
                $discount = Discount::find($validated['discount_id']);
                if ($discount && $discount->isValid()) {
                    $discountId = $discount->id;
                    if ($discount->type === 'percentage') {
                        $discountAmount = round(($subtotal * (float) $discount->value) / 100, 2);
                    } else {
                        $discountAmount = min($subtotal, (float) $discount->value);
                    }
                }
            }

            $totalAmount = max(0.00, $subtotal - $discountAmount);

            // Generate unique invoice number: INV-YYYYMMDD-XXXX
            $datePrefix = date('Ymd');
            $randomSuffix = strtoupper(Str::random(4));
            $invoiceNumber = "INV-{$datePrefix}-{$randomSuffix}";

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'student_id' => $validated['student_id'],
                'discount_id' => $discountId,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'status' => 'Unpaid',
                'created_by' => $request->user()->id,
            ]);

            foreach ($itemsData as $data) {
                $invoice->items()->create($data);
            }

            return redirect()->route('admin.invoices.show', $invoice)
                ->with('success', "Invoice {$invoice->invoice_number} generated successfully.");
        });
    }

    /**
     * Display invoice details, payment status, and option to record payments.
     */
    public function show(Invoice $invoice): View
    {
        $invoice->load([
            'student',
            'discount',
            'creator',
            'items.course',
            'payments.recordedBy',
        ]);

        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Printable view / receipt for an invoice.
     */
    public function print(Invoice $invoice): View
    {
        $invoice->load([
            'student',
            'discount',
            'creator',
            'items.course',
            'payments.recordedBy',
        ]);

        return view('admin.invoices.print', compact('invoice'));
    }
}
