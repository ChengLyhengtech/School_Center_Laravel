<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('student.invoices.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                        &larr; Back to Invoices
                    </a>
                    <span class="text-slate-300">/</span>
                    <span class="text-xs font-bold text-slate-700">{{ $invoice->invoice_number }}</span>
                </div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight">
                    Invoice {{ $invoice->invoice_number }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Print Invoice</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-flash-messages />

            <!-- Printable Invoice Card -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-12 shadow-sm space-y-8" id="printable-invoice">
                <!-- Institution & Invoice Header -->
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 border-b border-slate-200 pb-8">
                    <div>
                        <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                            {{ $school->name ?? 'School Center' }}
                        </h1>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm">
                            {{ $school->address ?? 'Main Campus, Phnom Penh' }}
                        </p>
                        <p class="text-xs text-slate-500 font-mono mt-0.5">
                            📞 {{ $school->phone ?? '+855 12 345 678' }} &bull; ✉️ {{ $school->email ?? 'info@school.edu' }}
                        </p>
                    </div>

                    <div class="sm:text-right">
                        <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider
                            @if($invoice->status === 'Paid') bg-emerald-100 text-emerald-800
                            @elseif($invoice->status === 'Partial') bg-amber-100 text-amber-800
                            @else bg-rose-100 text-rose-800 @endif">
                            {{ $invoice->status }}
                        </span>
                        <h3 class="text-lg font-mono font-black text-slate-900 mt-2">
                            {{ $invoice->invoice_number }}
                        </h3>
                        <p class="text-xs text-slate-500 font-mono mt-0.5">
                            Date: {{ $invoice->created_at?->format('M d, Y') }}
                        </p>
                    </div>
                </div>

                <!-- Billed To Details -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1">Billed Student</span>
                        <p class="text-sm font-extrabold text-slate-900">{{ $invoice->student->name }}</p>
                        <p class="text-slate-600 mt-0.5 font-mono">{{ $invoice->student->email }}</p>
                        <p class="text-slate-600 font-mono">{{ $invoice->student->phone ?? 'No phone' }}</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col justify-between">
                        <div>
                            <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1">Payment Balance Overview</span>
                            <div class="flex items-center justify-between text-slate-700 mt-1">
                                <span>Total Amount:</span>
                                <span class="font-bold font-mono text-slate-900">${{ number_format($invoice->total_amount, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-700">
                                <span>Paid To Date:</span>
                                <span class="font-bold font-mono text-emerald-600">${{ number_format($invoice->totalPaid(), 2) }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-200 font-extrabold text-sm">
                            <span class="text-slate-900">Remaining Due:</span>
                            <span class="font-mono {{ $invoice->remainingBalance() > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                ${{ number_format($invoice->remainingBalance(), 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Line Items Table (Requirement #3) -->
                <div>
                    <h4 class="font-extrabold text-slate-900 text-sm mb-3">Itemized Fee Breakdown</h4>
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[11px] font-bold">
                                    <th class="py-3 px-4">#</th>
                                    <th class="py-3 px-4">Description</th>
                                    <th class="py-3 px-4 text-right">Unit Price</th>
                                    <th class="py-3 px-4 text-center">Qty</th>
                                    <th class="py-3 px-4 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($invoice->items as $idx => $item)
                                    <tr>
                                        <td class="py-3 px-4 font-mono text-slate-400">{{ $idx + 1 }}</td>
                                        <td class="py-3 px-4">
                                            <p class="font-bold text-slate-800">{{ $item->description }}</p>
                                            @if($item->course)
                                                <p class="text-[11px] text-indigo-600 font-semibold">{{ $item->course->title }}</p>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-right font-mono text-slate-700">${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="py-3 px-4 text-center font-mono text-slate-700">{{ $item->quantity }}</td>
                                        <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">${{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center text-slate-400">No items specified.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Totals Calculation Box -->
                <div class="flex justify-end">
                    <div class="w-full sm:w-72 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Subtotal:</span>
                            <span class="font-mono font-bold text-slate-800">${{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        @if($invoice->discount_amount > 0)
                            <div class="flex items-center justify-between text-emerald-700">
                                <span>Discount ({{ $invoice->discount->code ?? 'Scholarship' }}):</span>
                                <span class="font-mono font-bold">-${{ number_format($invoice->discount_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between text-sm font-extrabold text-slate-900 pt-2 border-t border-slate-200">
                            <span>Invoice Total:</span>
                            <span class="font-mono">${{ number_format($invoice->total_amount, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs font-bold text-emerald-600">
                            <span>Paid Amount:</span>
                            <span class="font-mono">-${{ number_format($invoice->totalPaid(), 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm font-extrabold pt-2 border-t border-slate-200 {{ $invoice->remainingBalance() > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            <span>Balance Due:</span>
                            <span class="font-mono">${{ number_format($invoice->remainingBalance(), 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Associated Payment Receipts -->
                <div class="pt-6 border-t border-slate-100">
                    <h4 class="font-extrabold text-slate-900 text-sm mb-3">Associated Payment Receipts</h4>
                    <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden text-xs">
                        @forelse($invoice->payments as $payment)
                            <div class="p-3.5 px-4 flex items-center justify-between bg-slate-50/50">
                                <div>
                                    <span class="font-mono font-extrabold text-slate-900">{{ $payment->reference_number ?? '#' . $payment->id }}</span>
                                    <p class="text-[11px] text-slate-500 font-mono mt-0.5">
                                        {{ $payment->paid_at?->format('M d, Y h:i A') }} &bull; Method: {{ $payment->payment_method }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="font-mono font-extrabold text-sm text-emerald-600">+${{ number_format($payment->amount_paid, 2) }}</span>
                                    <p class="text-[10px] text-slate-400">Recorded by {{ $payment->recordedBy->name ?? 'Staff' }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-6 text-center">No payment transactions recorded for this invoice yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Terms Footer -->
                <div class="text-center pt-6 border-t border-slate-100 text-[11px] text-slate-400 space-y-1">
                    <p>Thank you for choosing {{ $school->name ?? 'our learning center' }}.</p>
                    <p>Please present this invoice number when settling payment via cash or bank deposit.</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
