<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Walk-In Invoice Generation') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Generate multi-item billing for courses, registration, textbooks, or materials.</p>
            </div>
            <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                &larr; Back to Invoices
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="invoiceGenerator()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <form action="{{ route('admin.invoices.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
                @csrf

                <!-- Student & Discount Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-4 border-b border-gray-100">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Billed Student <span class="text-red-500">*</span></label>
                        <select name="student_id" required class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Select Student --</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}" {{ old('student_id') == $st->id ? 'selected' : '' }}>
                                    {{ $st->name }} ({{ $st->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Apply Discount Code (Optional)</label>
                        <select name="discount_id" x-model="selectedDiscount" @change="recalculateTotals()" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- No Discount --</option>
                            @foreach($discounts as $d)
                                <option value="{{ $d->id }}" data-type="{{ $d->type }}" data-value="{{ $d->value }}">
                                    {{ $d->code }} ({{ $d->type === 'percentage' ? $d->value.'%' : '$'.$d->value }} off{{ $d->course ? ' on '.$d->course->title : ' global' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Line Items Section -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold uppercase text-gray-700 tracking-wider">Invoice Line Items</h3>
                            <p class="text-xs text-gray-500">Combine tuition fees, learning kits, textbooks, or custom fees.</p>
                        </div>
                        <button type="button" @click="addItem()" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-semibold rounded-lg transition">
                            + Add Line Item
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                                <div class="sm:col-span-5">
                                    <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Description <span class="text-red-500">*</span></label>
                                    <input type="text" :name="`items[${index}][description]`" x-model="item.description" required placeholder="e.g. Tuition Fee - English Level 5" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="sm:col-span-3">
                                    <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Associated Course</label>
                                    <select :name="`items[${index}][course_id]`" x-model="item.course_id" class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">(None / General)</option>
                                        @foreach($courses as $c)
                                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Unit Price ($) <span class="text-red-500">*</span></label>
                                    <input type="number" step="0.01" min="0" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" @input="recalculateTotals()" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="sm:col-span-1">
                                    <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Qty</label>
                                    <input type="number" min="1" :name="`items[${index}][quantity]`" x-model.number="item.quantity" @input="recalculateTotals()" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="sm:col-span-1 flex justify-end pb-1">
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-red-500 hover:text-red-700 p-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Total Calculations Summary Box -->
                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <div class="w-full sm:w-72 bg-gray-50 p-4 rounded-xl border border-gray-200 space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal:</span>
                            <span class="font-semibold" x-text="'$' + subtotal.toFixed(2)">$0.00</span>
                        </div>
                        <div class="flex justify-between text-emerald-600" x-show="discountAmount > 0">
                            <span>Discount:</span>
                            <span class="font-semibold" x-text="'-$' + discountAmount.toFixed(2)">-$0.00</span>
                        </div>
                        <div class="border-t pt-2 flex justify-between text-base font-bold text-gray-900">
                            <span>Total Due:</span>
                            <span class="text-indigo-600" x-text="'$' + totalDue.toFixed(2)">$0.00</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2 border rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                        Generate Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function invoiceGenerator() {
            return {
                items: [
                    { description: 'Tuition Fee', course_id: '', unit_price: 150.00, quantity: 1 }
                ],
                selectedDiscount: '',
                subtotal: 150.00,
                discountAmount: 0.00,
                totalDue: 150.00,

                addItem() {
                    this.items.push({ description: '', course_id: '', unit_price: 0, quantity: 1 });
                    this.recalculateTotals();
                },

                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                        this.recalculateTotals();
                    }
                },

                recalculateTotals() {
                    let sub = 0;
                    this.items.forEach(item => {
                        const price = parseFloat(item.unit_price) || 0;
                        const qty = parseInt(item.quantity) || 1;
                        sub += (price * qty);
                    });
                    this.subtotal = sub;

                    // Calculate discount
                    let disc = 0;
                    const selectEl = document.querySelector('select[name="discount_id"]');
                    if (selectEl && selectEl.selectedIndex > 0) {
                        const opt = selectEl.options[selectEl.selectedIndex];
                        const type = opt.getAttribute('data-type');
                        const val = parseFloat(opt.getAttribute('data-value')) || 0;

                        if (type === 'percentage') {
                            disc = (sub * val) / 100;
                        } else {
                            disc = Math.min(sub, val);
                        }
                    }
                    this.discountAmount = disc;
                    this.totalDue = Math.max(0, sub - disc);
                }
            };
        }
    </script>
</x-app-layout>
