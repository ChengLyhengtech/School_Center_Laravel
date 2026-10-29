<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Record Payment Transaction') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Collect walk-in cash or bank transfer payment against an open student invoice.</p>
            </div>
            <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                &larr; Back to Payments
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            <form action="{{ route('admin.payments.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Select Open Invoice <span class="text-red-500">*</span></label>
                    <select name="invoice_id" required class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Choose Invoice to pay --</option>
                        @foreach($invoices as $inv)
                            <option value="{{ $inv->id }}" {{ ($selectedInvoice && $selectedInvoice->id == $inv->id) ? 'selected' : '' }}>
                                {{ $inv->invoice_number }} - {{ $inv->student->name ?? 'Student' }} (Due: ${{ number_format($inv->remainingBalance(), 2) }} of ${{ number_format($inv->total_amount, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Amount to Collect ($ USD) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount_paid" value="{{ old('amount_paid', $selectedInvoice ? $selectedInvoice->remainingBalance() : '') }}" required placeholder="0.00" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-bold font-mono">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Payment Method <span class="text-red-500">*</span></label>
                        <select name="payment_method" required class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="Cash">Cash (Walk-In)</option>
                            <option value="Bank Transfer">Bank Transfer (ABA / KHQR / Wing)</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Reference / Transaction #</label>
                        <input type="text" name="reference_number" value="{{ old('reference_number') }}" placeholder="e.g. ABA-TX-84920" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Payment Received Timestamp <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="paid_at" value="{{ old('paid_at', date('Y-m-d\TH:i')) }}" required class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 border rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                        Confirm & Record Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
