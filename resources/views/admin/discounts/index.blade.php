<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Discounts & Promotional Vouchers') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Manage fixed/percentage discounts, global or course-specific restrictions, and usage limits.</p>
            </div>
            <a href="{{ route('admin.discounts.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Promo Code
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <!-- Discounts Table -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Promo Code</th>
                                <th class="px-6 py-4">Discount Value</th>
                                <th class="px-6 py-4">Scope / Course Restriction</th>
                                <th class="px-6 py-4">Validity Window</th>
                                <th class="px-6 py-4">Usage Limits</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($discounts as $disc)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-6 py-4">
                                        <span class="font-mono font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2.5 py-1 rounded-md text-xs tracking-wider">
                                            {{ $disc->code }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-gray-900">
                                        {{ $disc->type === 'percentage' ? number_format($disc->value, 0).'%' : '$'.number_format($disc->value, 2) }}
                                        <span class="text-xs text-gray-400 font-normal">({{ ucfirst($disc->type) }})</span>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        @if($disc->course)
                                            <span class="font-medium text-gray-800">Only: {{ $disc->course->title }}</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700">
                                                Global (All Courses)
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500 font-mono">
                                        <div>From: {{ $disc->start_date?->format('M d, Y') ?? 'Anytime' }}</div>
                                        <div>Until: {{ $disc->expires_date?->format('M d, Y') ?? 'No expiry' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <span class="font-semibold text-gray-800">{{ $disc->invoices_count }}</span>
                                        @if($disc->usage_limit)
                                            <span class="text-gray-400">/ {{ $disc->usage_limit }} max</span>
                                        @else
                                            <span class="text-gray-400">/ Unlimited</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                            {{ $disc->is_active && $disc->isValid() ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $disc->is_active && $disc->isValid() ? 'Active' : 'Expired / Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('admin.discounts.edit', $disc) }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.discounts.destroy', $disc) }}" method="POST" class="inline" onsubmit="return confirm('Delete this discount code?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-700">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-400 text-xs">
                                        No discount codes configured yet. Click "New Promo Code" to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($discounts->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $discounts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
