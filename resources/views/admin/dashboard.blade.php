<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Executive Admin Center') }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Comprehensive control over school branding, academics, enrollments, and finances.</p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.invoices.create') }}" class="inline-flex items-center px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Walk-In Invoice
                </a>
                <a href="{{ route('admin.payments.create') }}" class="inline-flex items-center px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Collect Payment
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Total Students -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-5 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Students</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_students'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-50 flex items-center justify-between text-xs">
                        <span class="text-gray-400">{{ $stats['total_teachers'] ?? 0 }} Instructors</span>
                        <a href="{{ route('admin.users.index', ['role' => 'student']) }}" class="text-indigo-600 font-medium hover:underline">View All &rarr;</a>
                    </div>
                </div>

                <!-- Active Batches -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-5 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Active Classes</p>
                            <p class="text-2xl font-bold text-indigo-700 mt-1">{{ $stats['active_classes'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-50 flex items-center justify-between text-xs">
                        <span class="text-gray-400">{{ $stats['total_courses'] ?? 0 }} Course Types</span>
                        <a href="{{ route('admin.classes.index') }}" class="text-indigo-600 font-medium hover:underline">Schedules &rarr;</a>
                    </div>
                </div>

                <!-- Collected Revenue -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-5 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Collected</p>
                            <p class="text-2xl font-bold text-emerald-600 mt-1 font-mono">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</p>
                        </div>
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-50 flex items-center justify-between text-xs">
                        <span class="text-emerald-700 font-semibold">Verified Receipts</span>
                        <a href="{{ route('admin.payments.index') }}" class="text-indigo-600 font-medium hover:underline">Audit Logs &rarr;</a>
                    </div>
                </div>

                <!-- Unpaid Invoices -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-5 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Unsettled Invoices</p>
                            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['unpaid_invoices'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-50 flex items-center justify-between text-xs">
                        <span class="text-gray-400 font-mono">Due: ${{ number_format($stats['pending_amount'] ?? 0, 2) }}</span>
                        <a href="{{ route('admin.invoices.index', ['status' => 'Unpaid']) }}" class="text-amber-600 font-semibold hover:underline">Collect &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Quick Navigation Hub -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <a href="{{ route('admin.courses.index') }}" class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:border-indigo-300 hover:shadow-md transition text-center group">
                    <div class="w-10 h-10 mx-auto rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <span class="block mt-2 font-bold text-xs text-gray-800">Courses</span>
                </a>

                <a href="{{ route('admin.classes.index') }}" class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:border-indigo-300 hover:shadow-md transition text-center group">
                    <div class="w-10 h-10 mx-auto rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <span class="block mt-2 font-bold text-xs text-gray-800">Classes</span>
                </a>

                <a href="{{ route('admin.enrollments.index') }}" class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:border-indigo-300 hover:shadow-md transition text-center group">
                    <div class="w-10 h-10 mx-auto rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <span class="block mt-2 font-bold text-xs text-gray-800">Placements</span>
                </a>

                <a href="{{ route('admin.attendances.index') }}" class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:border-indigo-300 hover:shadow-md transition text-center group">
                    <div class="w-10 h-10 mx-auto rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <span class="block mt-2 font-bold text-xs text-gray-800">Attendance</span>
                </a>

                <a href="{{ route('admin.roles-permissions.index') }}" class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:border-indigo-300 hover:shadow-md transition text-center group">
                    <div class="w-10 h-10 mx-auto rounded-lg bg-pink-50 text-pink-600 flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <span class="block mt-2 font-bold text-xs text-gray-800">RBAC</span>
                </a>

                <a href="{{ route('admin.settings.profile') }}" class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:border-indigo-300 hover:shadow-md transition text-center group">
                    <div class="w-10 h-10 mx-auto rounded-lg bg-gray-50 text-gray-600 flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="block mt-2 font-bold text-xs text-gray-800">School Profile</span>
                </a>
            </div>

            <!-- Activity Logs: Recent Invoices & Payments -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Invoices -->
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-bold text-gray-800 text-sm">Recent Invoices Generated</h3>
                        <a href="{{ route('admin.invoices.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">View All &rarr;</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recentInvoices as $inv)
                            <div class="p-4 hover:bg-gray-50 flex items-center justify-between transition">
                                <div>
                                    <a href="{{ route('admin.invoices.show', $inv) }}" class="font-mono font-bold text-xs text-indigo-600 hover:underline">
                                        {{ $inv->invoice_number }}
                                    </a>
                                    <p class="text-xs font-semibold text-gray-800 mt-0.5">{{ $inv->student->name ?? 'Student' }}</p>
                                    <p class="text-[11px] text-gray-400">By {{ $inv->creator->name ?? 'Staff' }} &bull; {{ $inv->created_at?->diffForHumans() }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-sm text-gray-900 font-mono">${{ number_format($inv->total_amount, 2) }}</span>
                                    <div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold
                                            {{ $inv->status === 'Paid' ? 'bg-emerald-100 text-emerald-800' : ($inv->status === 'Partial' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $inv->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-xs text-gray-400">No invoices generated yet.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Payments -->
                <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="font-bold text-gray-800 text-sm">Recent Payments Collected</h3>
                        <a href="{{ route('admin.payments.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">View All &rarr;</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recentPayments as $pay)
                            <div class="p-4 hover:bg-gray-50 flex items-center justify-between transition">
                                <div>
                                    <span class="text-xs font-bold text-gray-900">{{ $pay->invoice?->student?->name ?? 'Student' }}</span>
                                    <p class="text-[11px] text-gray-500 font-mono">Invoice: {{ $pay->invoice->invoice_number ?? '-' }}</p>
                                    <p class="text-[10px] text-gray-400">Method: {{ $pay->payment_method }} &bull; Recorded by: {{ $pay->recordedBy->name ?? 'Staff' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-sm text-emerald-600 font-mono">+${{ number_format($pay->amount_paid, 2) }}</span>
                                    <p class="text-[10px] text-gray-400 font-mono">{{ $pay->paid_at?->format('M d, H:i') }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-xs text-gray-400">No payment transactions recorded yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
