<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Invoices & Billing Operations') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Generate walk-in student invoices, apply discounts, and manage receivable balances.</p>
            </div>
            <a href="{{ route('admin.invoices.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Generate New Invoice
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.invoices.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ empty($statusFilter) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        All Invoices
                    </a>
                    <a href="{{ route('admin.invoices.index', ['status' => 'Unpaid']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $statusFilter === 'Unpaid' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Unpaid
                    </a>
                    <a href="{{ route('admin.invoices.index', ['status' => 'Partial']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $statusFilter === 'Partial' ? 'bg-amber-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Partial
                    </a>
                    <a href="{{ route('admin.invoices.index', ['status' => 'Paid']) }}" class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $statusFilter === 'Paid' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        Paid
                    </a>
                </div>

                <form method="GET" action="{{ route('admin.invoices.index') }}" class="flex items-center space-x-2">
                    @if($statusFilter)
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                    @endif
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search invoice # or student..." class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 w-60">
                    <button type="submit" class="px-3 py-1.5 bg-gray-800 text-white text-xs rounded-lg hover:bg-gray-900 transition">Search</button>
                    @if($search)
                        <a href="{{ route('admin.invoices.index', ['status' => $statusFilter]) }}" class="text-xs text-gray-500 hover:text-gray-800 underline">Clear</a>
                    @endif
                </form>
            </div>

            <!-- Invoices Table -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Invoice #</th>
                                <th class="px-6 py-4">Student</th>
                                <th class="px-6 py-4">Subtotal / Discount</th>
                                <th class="px-6 py-4">Total Amount</th>
                                <th class="px-6 py-4">Remaining Balance</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($invoices as $inv)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.invoices.show', $inv) }}" class="font-mono font-bold text-gray-900 hover:text-indigo-600 text-xs">
                                            {{ $inv->invoice_number }}
                                        </a>
                                        <p class="text-[11px] text-gray-400 mt-0.5">{{ $inv->created_at?->format('M d, Y') }} &bull; By {{ $inv->creator->name ?? 'Staff' }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.users.show', $inv->student_id) }}" class="font-semibold text-gray-900 hover:text-indigo-600">
                                            {{ $inv->student->name ?? 'Unknown Student' }}
                                        </a>
                                        <p class="text-xs text-gray-400">{{ $inv->student->email ?? '' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <div class="text-gray-900">${{ number_format($inv->subtotal, 2) }}</div>
                                        @if($inv->discount_amount > 0)
                                            <div class="text-emerald-600 text-[11px]">-${{ number_format($inv->discount_amount, 2) }} ({{ $inv->discount->code ?? 'Promo' }})</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-900">
                                        ${{ number_format($inv->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-xs {{ $inv->remainingBalance() > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                        ${{ number_format($inv->remainingBalance(), 2) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                            {{ $inv->status === 'Paid' ? 'bg-emerald-100 text-emerald-800' : ($inv->status === 'Partial' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $inv->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('admin.invoices.show', $inv) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                            View / Pay
                                        </a>
                                        <a href="{{ route('admin.invoices.print', $inv) }}" target="_blank" class="text-xs font-semibold text-gray-600 hover:text-gray-900">
                                            Receipt
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-400 text-xs">
                                        No invoices found matching criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($invoices->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $invoices->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
