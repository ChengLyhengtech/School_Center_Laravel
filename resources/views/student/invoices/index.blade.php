<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight">
                    {{ __('Invoices & Payment History') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Review your tuition invoices, line-item fees, payment receipts, and balance dues.</p>
            </div>
            <div>
                <a href="{{ route('student.catalog.index') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <span>Explore Courses</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-flash-messages />

            <!-- Financial Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Invoiced Billed</span>
                    <p class="text-3xl font-extrabold text-slate-900 mt-1">${{ number_format($totalBilled, 2) }}</p>
                    <span class="text-xs font-medium text-slate-500">Gross tuition and materials</span>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Paid To Date</span>
                    <p class="text-3xl font-extrabold text-emerald-600 mt-1">${{ number_format($totalPaid, 2) }}</p>
                    <span class="text-xs font-medium text-slate-500">Confirmed payment receipts</span>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Outstanding Balance Due</span>
                    <p class="text-3xl font-extrabold {{ $totalBalanceDue > 0 ? 'text-rose-600' : 'text-emerald-600' }} mt-1">
                        ${{ number_format($totalBalanceDue, 2) }}
                    </p>
                    <span class="text-xs font-medium text-slate-500">
                        {{ $totalBalanceDue > 0 ? 'Payable at center cash counter' : 'Account in good standing' }}
                    </span>
                </div>
            </div>

            <!-- Invoices List Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Tuition & Material Invoices</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Click any invoice to view detailed itemized fees and discounts</p>
                    </div>

                    <!-- Status Filter Tabs -->
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('student.invoices.index', ['status' => 'all']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $status === 'all' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                            All
                        </a>
                        <a href="{{ route('student.invoices.index', ['status' => 'Unpaid']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $status === 'Unpaid' ? 'bg-rose-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                            Unpaid
                        </a>
                        <a href="{{ route('student.invoices.index', ['status' => 'Partial']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $status === 'Partial' ? 'bg-amber-500 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                            Partial
                        </a>
                        <a href="{{ route('student.invoices.index', ['status' => 'Paid']) }}" class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $status === 'Paid' ? 'bg-emerald-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
                            Paid
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80">
                                <th class="py-3 px-6">Invoice #</th>
                                <th class="py-3 px-6">Billed Date</th>
                                <th class="py-3 px-6">Items Enclosed</th>
                                <th class="py-3 px-6 text-right">Subtotal</th>
                                <th class="py-3 px-6 text-right">Discount</th>
                                <th class="py-3 px-6 text-right">Total Billed</th>
                                <th class="py-3 px-6 text-right">Amount Paid</th>
                                <th class="py-3 px-6 text-right">Remaining Balance</th>
                                <th class="py-3 px-6 text-center">Status</th>
                                <th class="py-3 px-6 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($invoices as $invoice)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-4 px-6 font-mono font-extrabold text-indigo-600">
                                        {{ $invoice->invoice_number }}
                                    </td>
                                    <td class="py-4 px-6 font-mono text-slate-600">
                                        {{ $invoice->created_at?->format('M d, Y') }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-semibold text-slate-800">
                                            {{ $invoice->items->first()?->description ?? 'Tuition fee' }}
                                        </div>
                                        @if($invoice->items->count() > 1)
                                            <span class="text-[10px] text-slate-400">+{{ $invoice->items->count() - 1 }} more item(s)</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right font-mono text-slate-600">
                                        ${{ number_format($invoice->subtotal, 2) }}
                                    </td>
                                    <td class="py-4 px-6 text-right font-mono text-emerald-600">
                                        -${{ number_format($invoice->discount_amount, 2) }}
                                    </td>
                                    <td class="py-4 px-6 text-right font-mono font-bold text-slate-900">
                                        ${{ number_format($invoice->total_amount, 2) }}
                                    </td>
                                    <td class="py-4 px-6 text-right font-mono font-bold text-emerald-600">
                                        ${{ number_format($invoice->totalPaid(), 2) }}
                                    </td>
                                    <td class="py-4 px-6 text-right font-mono font-bold {{ $invoice->remainingBalance() > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                        ${{ number_format($invoice->remainingBalance(), 2) }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="px-2.5 py-1 text-xs font-extrabold rounded-full
                                            @if($invoice->status === 'Paid') bg-emerald-100 text-emerald-800
                                            @elseif($invoice->status === 'Partial') bg-amber-100 text-amber-800
                                            @else bg-rose-100 text-rose-800 @endif">
                                            {{ $invoice->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <a href="{{ route('student.invoices.show', $invoice) }}" class="px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-xs transition-colors inline-block">
                                            View Receipt &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="py-12 text-center text-slate-400">
                                        No invoices found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment Receipt History Section (Requirement #3) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Official Payment Receipt History</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Recorded cash or bank transfer payment transactions credited to your invoices</p>
                    </div>
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-lg">
                        {{ $payments->count() }} Payments Recorded
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80">
                                <th class="py-3 px-6">Receipt #</th>
                                <th class="py-3 px-6">Payment Date</th>
                                <th class="py-3 px-6">Applied To Invoice</th>
                                <th class="py-3 px-6">Payment Method</th>
                                <th class="py-3 px-6">Cashier / Staff</th>
                                <th class="py-3 px-6 text-right">Amount Credited</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($payments as $pmt)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-3.5 px-6 font-mono font-bold text-slate-900">
                                        {{ $pmt->reference_number ?? ('REC-' . str_pad($pmt->id, 5, '0', STR_PAD_LEFT)) }}
                                    </td>
                                    <td class="py-3.5 px-6 font-mono text-slate-600">
                                        {{ $pmt->paid_at?->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="py-3.5 px-6 font-mono font-semibold text-indigo-600">
                                        <a href="{{ route('student.invoices.show', $pmt->invoice_id) }}" class="hover:underline">
                                            {{ $pmt->invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="py-3.5 px-6">
                                        <span class="px-2.5 py-0.5 rounded-md text-xs font-bold bg-slate-100 text-slate-700">
                                            {{ $pmt->payment_method }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-6 text-slate-600">
                                        {{ $pmt->recordedBy->name ?? 'Front Desk Staff' }}
                                    </td>
                                    <td class="py-3.5 px-6 text-right font-mono font-extrabold text-sm text-emerald-600">
                                        +${{ number_format($pmt->amount_paid, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        No payment receipts recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
