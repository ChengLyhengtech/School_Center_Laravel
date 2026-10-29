<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    Edit Promo Code: {{ $discount->code }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Update discount rules, limits, or validity window.</p>
            </div>
            <a href="{{ route('admin.discounts.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                &larr; Back to Discounts
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-flash-messages />

            <form action="{{ route('admin.discounts.update', $discount) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Promo Code (Uppercase) <span class="text-red-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code', $discount->code) }}" required class="w-full text-sm font-mono uppercase font-bold rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Applicable Course Scope</label>
                        <select name="course_id" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">(Global - Applies to Any Course)</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}" {{ old('course_id', $discount->course_id) == $c->id ? 'selected' : '' }}>
                                    Restrict to: {{ $c->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Discount Type <span class="text-red-500">*</span></label>
                        <select name="type" required class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="percentage" {{ old('type', $discount->type) === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                            <option value="fixed" {{ old('type', $discount->type) === 'fixed' ? 'selected' : '' }}>Fixed Amount ($ USD)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Discount Value <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="value" value="{{ old('value', $discount->value) }}" required class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-mono font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Valid From Date</label>
                        <input type="datetime-local" name="start_date" value="{{ old('start_date', $discount->start_date ? $discount->start_date->format('Y-m-d\TH:i') : '') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Expires Date</label>
                        <input type="datetime-local" name="expires_date" value="{{ old('expires_date', $discount->expires_date ? $discount->expires_date->format('Y-m-d\TH:i') : '') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Max Total Usage Limit</label>
                        <input type="number" min="1" name="usage_limit" value="{{ old('usage_limit', $discount->usage_limit) }}" placeholder="Leave blank for unlimited" class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="pt-4">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $discount->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-bold text-gray-700">Promo Code is Active</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                    <a href="{{ route('admin.discounts.index') }}" class="px-4 py-2 border rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                        Update Promo Code
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
