<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight">
                    {{ __('Attendance & Academic Monitoring') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Review session attendance records and verify minimum participation requirements.</p>
            </div>
            <div>
                <a href="{{ route('student.classes.index') }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all shadow-2xs">
                    View Class Timetable
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-flash-messages />

            <!-- KPI Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Overall Presence Rate</span>
                    <p class="text-2xl sm:text-3xl font-extrabold {{ ($attendanceRate ?? 0) >= 80 ? 'text-emerald-600' : 'text-amber-600' }} mt-1">
                        {{ $attendanceRate !== null ? "{$attendanceRate}%" : 'N/A' }}
                    </p>
                    <span class="text-xs font-medium text-slate-500">Center target &ge; 80%</span>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Present Sessions</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1">{{ $presentCount }}</p>
                    <span class="text-xs font-medium text-slate-500">On-time classroom logs</span>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Late Arrivals</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-amber-500 mt-1">{{ $lateCount }}</p>
                    <span class="text-xs font-medium text-slate-500">Recorded tardiness</span>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Absences</span>
                    <p class="text-2xl sm:text-3xl font-extrabold {{ $absentCount >= 3 ? 'text-rose-600' : 'text-slate-800' }} mt-1">{{ $absentCount }}</p>
                    <span class="text-xs font-medium text-slate-500">
                        {{ $absentCount >= 3 ? '⚠️ High absence risk' : 'Normal range' }}
                    </span>
                </div>
            </div>

            <!-- Attendance Filter Bar -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <form method="GET" action="{{ route('student.attendance.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <div class="md:col-span-5">
                        <label for="class_id" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Filter By Class Section</label>
                        <select id="class_id" name="class_id" onchange="this.form.submit()" class="w-full rounded-xl border-slate-300 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500">
                            <option value="">All Enrolled Classes</option>
                            @foreach($enrolledClasses as $c)
                                <option value="{{ $c->id }}" {{ (string) request('class_id') === (string) $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->course->title }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label for="start_date" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">From Date</label>
                        <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="w-full rounded-xl border-slate-300 text-xs focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500">
                    </div>

                    <div class="md:col-span-3">
                        <label for="end_date" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">To Date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="w-full rounded-xl border-slate-300 text-xs focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500">
                    </div>

                    <div class="md:col-span-1">
                        <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-2xs transition-all">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Course-by-Course Attendance Consistency Summary -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Course Attendance Consistency Summary</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Participation compliance status per enrolled class batch</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80">
                                <th class="py-3 px-6">Class Section</th>
                                <th class="py-3 px-6">Course Title</th>
                                <th class="py-3 px-6 text-center">Sessions Logged</th>
                                <th class="py-3 px-6 text-center">Present</th>
                                <th class="py-3 px-6 text-center">Late</th>
                                <th class="py-3 px-6 text-center">Absent</th>
                                <th class="py-3 px-6 text-center">Presence %</th>
                                <th class="py-3 px-6 text-right">Compliance Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            @forelse($classBreakdown as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-4 px-6 font-bold text-slate-900">
                                        {{ $item->class->name }}
                                    </td>
                                    <td class="py-4 px-6 text-indigo-600 font-semibold">
                                        {{ $item->class->course->title }}
                                    </td>
                                    <td class="py-4 px-6 text-center font-mono font-bold">
                                        {{ $item->total }}
                                    </td>
                                    <td class="py-4 px-6 text-center text-emerald-600 font-bold">
                                        {{ $item->present }}
                                    </td>
                                    <td class="py-4 px-6 text-center text-amber-500 font-bold">
                                        {{ $item->late }}
                                    </td>
                                    <td class="py-4 px-6 text-center text-rose-600 font-bold">
                                        {{ $item->absent }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="font-bold {{ ($item->rate ?? 0) >= 80 ? 'text-emerald-600' : 'text-amber-600' }}">
                                            {{ $item->rate !== null ? "{$item->rate}%" : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        @if($item->rate === null)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600">Pending</span>
                                        @elseif($item->rate >= 80)
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                ✓ Satisfactory
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                                ⚠️ Below Target (80%)
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-slate-400">
                                        No active class enrollments found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Detailed Chronological Log Table -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Session-by-Session Attendance Logs</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $attendances->count() }} total session records</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/80">
                                <th class="py-3 px-6">Session Date</th>
                                <th class="py-3 px-6">Class Section</th>
                                <th class="py-3 px-6">Course</th>
                                <th class="py-3 px-6">Classroom</th>
                                <th class="py-3 px-6">Instructor</th>
                                <th class="py-3 px-6 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($attendances as $att)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-3.5 px-6 font-mono font-bold text-slate-800">
                                        {{ $att->date?->format('l, M d, Y') }}
                                    </td>
                                    <td class="py-3.5 px-6 font-semibold text-slate-900">
                                        {{ $att->class->name }}
                                    </td>
                                    <td class="py-3.5 px-6 text-slate-600">
                                        {{ $att->class->course->title }}
                                    </td>
                                    <td class="py-3.5 px-6 text-slate-500">
                                        📍 {{ $att->class->room ?? 'TBA' }}
                                    </td>
                                    <td class="py-3.5 px-6 text-slate-600">
                                        {{ $att->class->teacher->name ?? 'Faculty' }}
                                    </td>
                                    <td class="py-3.5 px-6 text-right">
                                        <span class="px-2.5 py-1 text-xs font-extrabold rounded-full
                                            @if($att->status === 'Present') bg-emerald-100 text-emerald-800
                                            @elseif($att->status === 'Late') bg-amber-100 text-amber-800
                                            @else bg-rose-100 text-rose-800 @endif">
                                            {{ $att->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        No attendance sessions match the selected filter criteria.
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
