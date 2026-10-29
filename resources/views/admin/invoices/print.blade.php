<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $invoice->invoice_number }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print { display: none; }
            body { font-size: 12px; }
        }
    </style>
</head>
<body class="bg-gray-100 p-6 font-sans">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow border border-gray-200">
        <!-- Action Buttons -->
        <div class="no-print mb-6 flex justify-between items-center">
            <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-xs text-indigo-600 hover:underline">&larr; Return to Invoice</a>
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg shadow transition flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Official Receipt
            </button>
        </div>

        @php
            $profile = \App\Models\SchoolProfile::first();
        @endphp

        <!-- School Receipt Header -->
        <div class="flex justify-between items-start border-b pb-6">
            <div>
                <h1 class="text-xl font-black uppercase text-gray-900 tracking-wider">{{ $profile->name ?? 'CLH School Center' }}</h1>
                <p class="text-xs text-gray-500 mt-0.5">{{ $profile->tagline ?? 'Excellence in Technology and Language Education' }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $profile->address ?? 'Phnom Penh, Cambodia' }}</p>
                <p class="text-xs text-gray-500">Tel: {{ $profile->phone ?? '+855 12 345 678' }} &bull; Email: {{ $profile->email ?? 'info@school.edu' }}</p>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-bold text-indigo-700 uppercase">Official Receipt</h2>
                <p class="text-xs font-mono font-bold text-gray-800 mt-1">{{ $invoice->invoice_number }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Date: {{ $invoice->created_at?->format('M d, Y') }}</p>
                <span class="inline-block mt-2 px-2.5 py-0.5 text-xs font-bold rounded-full {{ $invoice->status === 'Paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    Status: {{ $invoice->status }}
                </span>
            </div>
        </div>

        <!-- Student Info -->
        <div class="my-6 p-4 bg-gray-50 rounded-lg text-xs space-y-1">
            <div class="font-bold uppercase text-gray-500">Billed To / Student Information:</div>
            <div class="text-sm font-bold text-gray-900">{{ $invoice->student->name ?? 'Student' }}</div>
            <div class="text-gray-600">Email: {{ $invoice->student->email ?? '-' }}</div>
            @if($invoice->student?->parents->isNotEmpty())
                <div class="text-gray-600">Guardian / Parent: {{ $invoice->student->parents->pluck('name')->join(', ') }}</div>
            @endif
        </div>

        <!-- Items Table -->
        <table class="w-full text-left text-xs mb-6">
            <thead>
                <tr class="border-b-2 border-gray-200 text-gray-600 uppercase font-semibold">
                    <th class="py-2">Description</th>
                    <th class="py-2 text-right">Unit Price</th>
                    <th class="py-2 text-center">Qty</th>
                    <th class="py-2 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($invoice->items as $item)
                    <tr>
                        <td class="py-2.5 font-medium text-gray-800">{{ $item->description }}</td>
                        <td class="py-2.5 text-right font-mono">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-2.5 text-center font-mono">{{ $item->quantity }}</td>
                        <td class="py-2.5 text-right font-bold font-mono">${{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="border-t pt-4 flex justify-end">
            <div class="w-56 text-xs space-y-1.5">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal:</span>
                    <span class="font-mono font-semibold">${{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                @if($invoice->discount_amount > 0)
                    <div class="flex justify-between text-emerald-600">
                        <span>Discount ({{ $invoice->discount->code ?? 'Promo' }}):</span>
                        <span class="font-mono font-semibold">-${{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="border-t pt-1.5 flex justify-between font-bold text-sm text-gray-900">
                    <span>Total Amount:</span>
                    <span class="font-mono text-indigo-700">${{ number_format($invoice->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-emerald-700 font-semibold">
                    <span>Amount Paid:</span>
                    <span class="font-mono">${{ number_format($invoice->totalPaid(), 2) }}</span>
                </div>
                <div class="flex justify-between text-red-600 font-bold border-t pt-1">
                    <span>Balance Due:</span>
                    <span class="font-mono">${{ number_format($invoice->remainingBalance(), 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Payments Details -->
        @if($invoice->payments->isNotEmpty())
            <div class="mt-6 pt-4 border-t text-xs">
                <h3 class="font-bold text-gray-700 uppercase mb-2">Payment Transaction Details:</h3>
                <div class="space-y-1">
                    @foreach($invoice->payments as $p)
                        <div class="flex justify-between text-gray-600">
                            <span>{{ $p->paid_at?->format('M d, Y') }} &bull; {{ $p->payment_method }} (Ref: {{ $p->reference_number ?? 'N/A' }})</span>
                            <span class="font-bold text-emerald-600 font-mono">+${{ number_format($p->amount_paid, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Signatures -->
        <div class="mt-12 pt-8 border-t border-dashed flex justify-between text-xs text-gray-500">
            <div class="text-center">
                <div class="w-36 border-b border-gray-400 mb-1"></div>
                <span>Student / Guardian Signature</span>
            </div>
            <div class="text-center">
                <div class="w-36 border-b border-gray-400 mb-1"></div>
                <span>Cashier / Staff Signature</span>
                <p class="text-[10px] text-gray-400 mt-0.5">({{ $invoice->creator->name ?? 'Staff Admin' }})</p>
            </div>
        </div>
    </div>
</body>
</html>
