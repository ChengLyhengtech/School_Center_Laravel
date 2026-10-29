<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('teacher.attendance.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">← Attendance Hub</a>
                </div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight mt-1">
                    {{ __('Attendance History & Truancy Trends') }}
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">Track attendance consistency, session breakdown, and truancy alerts.</p>
            </div>
            @if($selectedClass)
                <div>
                    <a href="{{ route('teacher.attendance.create', ['class_id' => $selectedClass->id]) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Take Today's Attendance</span>
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-flash-messages />

            <!-- Filter Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <form method="GET" action="{{ route('teacher.attendance.history') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <div class="md:col-span-5">
                        <label for="class_id" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Class Section</label>
                        <select id="class_id" name="class_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 font-medium">
                            <option value="">-- Choose an assigned class --</option>
                            @foreach($myClasses as $c)
                                <option value="{{ $c->id }}" {{ (string) request('class_id') === (string) $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->course->title }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label for="start_date" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">From Date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div class="md:col-span-3">
                        <label for="end_date" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">To Date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div class="md:col-span-1">
                        <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-2xs transition-all">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            @if($selectedClass)
                @php
                    $truantCount = $studentAttendanceSummaries->where('is_truant', true)->count();
                    $avgConsistency = $studentAttendanceSummaries->avg('rate');
                @endphp

                <!-- Truancy Alert Banner if any student has >= 3 absences -->
                @if($truantCount > 0)
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3 shadow-sm">
                        <div class="text-xl shrink-0">⚠️</div>
                        <div>
                            <div class="font-extrabold text-sm text-amber-950">Truancy Risk Alert Detected</div>
                            <p class="text-xs text-amber-800 mt-0.5">
                                {{ $truantCount }} student(s) in this class have accumulated 3 or more absences. Immediate intervention or parental notification recommended.
                            </p>
                        </div>
                    </div>
                @endif

                <!-- KPI Metric Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sessions Recorded</div>
                        <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ $attendanceDates->count() }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">Distinct class dates</div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Class Avg Attendance</div>
                        <div class="text-2xl font-extrabold {{ ($avgConsistency ?? 0) >= 80 ? 'text-emerald-600' : 'text-amber-600' }} mt-1">
                            {{ $avgConsistency !== null ? round($avgConsistency, 1) . '%' : 'N/A' }}
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5">Overall student presence</div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Truancy Watchlist</div>
                        <div class="text-2xl font-extrabold {{ $truantCount > 0 ? 'text-rose-600' : 'text-emerald-600' }} mt-1">
                            {{ $truantCount }}
                        </div>
                        <div class="text-xs text-slate-500 mt-0.5">Students with &ge; 3 absences</div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Enrolled Roster</div>
                        <div class="text-2xl font-extrabold text-slate-900 mt-1">{{ $studentAttendanceSummaries->count() }}</div>
                        <div class="text-xs text-slate-500 mt-0.5">Students in section</div>
                    </div>
                </div>

                <!-- Student Consistency & Truancy Trends Table -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base">Student Consistency & Truancy Log</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Aggregated individual attendance history and status flags</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80">
                                    <th class="py-3 px-6">Student</th>
                                    <th class="py-3 px-6 text-center">Sessions</th>
                                    <th class="py-3 px-6 text-center">Present</th>
                                    <th class="py-3 px-6 text-center">Late</th>
                                    <th class="py-3 px-6 text-center">Absent</th>
                                    <th class="py-3 px-6 text-center">Attendance %</th>
                                    <th class="py-3 px-6 text-right">Trend Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                @forelse($studentAttendanceSummaries as $item)
                                    <tr class="hover:bg-slate-50/60 transition-colors {{ $item->is_truant ? 'bg-rose-50/30' : '' }}">
                                        <td class="py-4 px-6">
                                            <div class="font-bold text-slate-900 leading-snug">{{ $item->student->name }}</div>
                                            <div class="text-xs text-slate-400">{{ $item->student->email }}</div>
                                        </td>
                                        <td class="py-4 px-6 text-center font-mono text-xs text-slate-700">
                                            {{ $item->total }}
                                        </td>
                                        <td class="py-4 px-6 text-center font-bold text-emerald-600">
                                            {{ $item->present }}
                                        </td>
                                        <td class="py-4 px-6 text-center font-bold text-amber-500">
                                            {{ $item->late }}
                                        </td>
                                        <td class="py-4 px-6 text-center font-bold text-rose-600">
                                            {{ $item->absent }}
                                        </td>
                                        <td class="py-4 px-6 text-center">
                                            <div class="inline-flex items-center gap-1.5 font-bold text-xs {{ $item->rate >= 80 ? 'text-emerald-700' : ($item->rate >= 60 ? 'text-amber-700' : 'text-rose-700') }}">
                                                <span>{{ $item->rate }}%</span>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            @if($item->is_truant)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-700 border border-rose-200">
                                                    ⚠️ Truancy Alert
                                                </span>
                                            @elseif($item->rate >= 80)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                                    Consistent
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                                    Moderate
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-12 text-center text-slate-400">
                                            No students found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Past Recorded Dates Breakdown -->
                <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                    <h3 class="font-extrabold text-slate-900 text-base mb-1">Session Log History</h3>
                    <p class="text-xs text-slate-500 mb-4">Click any date to inspect or modify the recorded attendance sheet.</p>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        @forelse($attendanceDates as $dateItem)
                            <a href="{{ route('teacher.attendance.create', ['class_id' => $selectedClass->id, 'date' => $dateItem]) }}" class="p-3 rounded-xl border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/40 transition-all text-center group">
                                <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-700 font-mono">
                                    {{ \Carbon\Carbon::parse($dateItem)->format('M d, Y') }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($dateItem)->format('l') }}
                                </div>
                                <div class="mt-2 text-[10px] font-bold text-emerald-600">
                                    Edit Session &rarr;
                                </div>
                            </a>
                        @empty
                            <div class="col-span-full py-8 text-center text-slate-400 text-xs">
                                No sessions recorded in this date range.
                            </div>
                        @endforelse
                    </div>
                </div>
            @else
                <div class="py-16 text-center text-slate-500 bg-white rounded-2xl border border-dashed border-slate-300">
                    <p class="font-semibold text-slate-700">Please choose a class section above to view its attendance history.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
