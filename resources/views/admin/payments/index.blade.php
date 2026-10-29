<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Payment Transactions & Financial Audit Logs') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Track every collected fee, payment method, reference number, and staff accountability.</p>
            </div>
            <a href="{{ route('admin.payments.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Record Payment
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <!-- Summary KPI -->
            <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-xl p-6 text-white shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-100">Total Lifetime Collected Revenue</p>
                    <p class="text-3xl font-black mt-1 font-mono">${{ number_format($totalCollected, 2) }}</p>
                </div>
                <div class="text-xs text-emerald-100">
                    All payment records are locked and linked to the collecting staff member.
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.payments.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ empty($methodFilter) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        All Methods
                    </a>
                    <a href="{{ route('admin.payments.index', ['payment_method' => 'Cash']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $methodFilter === 'Cash' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Cash
                    </a>
                    <a href="{{ route('admin.payments.index', ['payment_method' => 'Bank Transfer']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $methodFilter === 'Bank Transfer' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Bank Transfer
                    </a>
                </div>

                <form method="GET" action="{{ route('admin.payments.index') }}" class="flex items-center space-x-2">
                    @if($methodFilter)
                        <input type="hidden" name="payment_method" value="{{ $methodFilter }}">
                    @endif
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search ref #, invoice, student..." class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 w-60">
                    <button type="submit" class="px-3 py-1.5 bg-gray-800 text-white text-xs rounded-lg hover:bg-gray-900 transition">Search</button>
                    @if($search)
                        <a href="{{ route('admin.payments.index', ['payment_method' => $methodFilter]) }}" class="text-xs text-gray-500 hover:text-gray-800 underline">Clear</a>
                    @endif
                </form>
            </div>

            <!-- Payments Table -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Receipt / Invoice #</th>
                                <th class="px-6 py-4">Student</th>
                                <th class="px-6 py-4">Method & Ref #</th>
                                <th class="px-6 py-4">Staff (Recorded By)</th>
                                <th class="px-6 py-4">Paid Timestamp</th>
                                <th class="px-6 py-4 text-right">Amount Collected</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($payments as $pay)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.invoices.show', $pay->invoice_id) }}" class="font-mono font-bold text-indigo-600 hover:underline text-xs">
                                            {{ $pay->invoice->invoice_number ?? 'Invoice #'.$pay->invoice_id }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.users.show', $pay->invoice?->student_id) }}" class="font-semibold text-gray-900 hover:text-indigo-600">
                                            {{ $pay->invoice?->student?->name ?? 'Unknown Student' }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800">
                                            {{ $pay->payment_method }}
                                        </span>
                                        @if($pay->reference_number)
                                            <div class="text-[11px] font-mono text-gray-400 mt-0.5">Ref: {{ $pay->reference_number }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-gray-700">
                                        <div class="flex items-center space-x-1.5">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <span>{{ $pay->recordedBy->name ?? 'Staff Admin' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500 font-mono">
                                        {{ $pay->paid_at?->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-emerald-600 font-mono text-base">
                                        +${{ number_format($pay->amount_paid, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-400 text-xs">
                                        No payment transaction logs recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($payments->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
