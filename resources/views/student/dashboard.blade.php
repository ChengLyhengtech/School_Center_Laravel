<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight">
                    {{ __('Student Learning Portal') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Welcome back, {{ $student->name }}! Track your class timetable, attendance consistency, and invoices.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                    Enrolled Student
                </span>
                <a href="{{ route('student.catalog.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm shadow-indigo-600/30 transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Browse Courses</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-flash-messages />

            <!-- KPI Metric Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Enrolled Classes -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Enrolled Batches</span>
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $enrolledClasses->count() }}</p>
                        <span class="text-xs font-medium text-blue-600">Active class sections</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                </div>

                <!-- Overall Attendance Rate -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Attendance Rate</span>
                        <p class="text-2xl sm:text-3xl font-extrabold {{ ($attendanceRate ?? 0) >= 80 ? 'text-emerald-600' : 'text-amber-600' }} mt-1">
                            {{ $attendanceRate !== null ? "{$attendanceRate}%" : 'No logs' }}
                        </p>
                        <span class="text-xs font-medium text-slate-500">{{ $presentSessions }} / {{ $totalSessions }} Sessions present</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <!-- Academic Average -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Grade Average</span>
                        <p class="text-2xl sm:text-3xl font-extrabold text-indigo-600 mt-1">
                            {{ $gradeAverage !== null ? "{$gradeAverage}%" : 'No grades' }}
                        </p>
                        <span class="text-xs font-medium text-slate-500">{{ $recentGrades->count() }} Recorded evaluations</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                </div>

                <!-- Outstanding Balance Due -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Balance Due</span>
                        <p class="text-2xl sm:text-3xl font-extrabold {{ $totalBalanceDue > 0 ? 'text-rose-600' : 'text-emerald-600' }} mt-1">
                            ${{ number_format($totalBalanceDue, 2) }}
                        </p>
                        <span class="text-xs font-medium text-slate-500">
                            {{ $unpaidInvoicesCount > 0 ? "{$unpaidInvoicesCount} Invoices pending" : 'All accounts settled' }}
                        </span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Enrolled Classes Hub (Requirement #1) -->
            <div class="bg-white shadow-sm rounded-2xl border border-slate-200/80 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">My Enrolled Classes & Schedule</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Active timetable, classrooms, and assigned instructor contacts</p>
                    </div>
                    <a href="{{ route('student.classes.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                        View All Classes &rarr;
                    </a>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($enrolledClasses as $class)
                        <div class="border border-slate-200/80 rounded-2xl p-5 hover:border-blue-400 hover:shadow-md transition-all bg-gradient-to-b from-white to-slate-50/50 flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-md {{ $class->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ ucfirst($class->status) }}
                                    </span>
                                    <span class="text-xs font-bold px-2.5 py-0.5 rounded bg-blue-50 text-blue-700">
                                        📍 {{ $class->room ?? 'Room TBA' }}
                                    </span>
                                </div>

                                <h4 class="font-extrabold text-slate-900 text-base mt-3 leading-snug">
                                    {{ $class->name }}
                                </h4>
                                <p class="text-xs font-semibold text-indigo-600 mt-0.5">
                                    {{ $class->course->title }}
                                </p>

                                <!-- Schedule Timetable & Instructor -->
                                <div class="mt-4 pt-3 border-t border-slate-100 text-xs space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500 font-medium">Study Days:</span>
                                        <span class="font-bold text-slate-800">{{ $class->schedule_day ?? 'TBA' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500 font-medium">Time Slot:</span>
                                        <span class="font-bold text-slate-800">{{ $class->schedule_time ?? 'TBA' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500 font-medium">Instructor:</span>
                                        <span class="font-bold text-slate-800">{{ $class->teacher->name ?? 'Faculty Assigned' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <a href="{{ route('student.classes.show', $class) }}" class="w-full text-center py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-2xs transition-colors">
                                    View Syllabus & Details &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-slate-500">
                            <p class="font-bold text-slate-700">You are not enrolled in any class batches yet.</p>
                            <p class="text-xs text-slate-400 mt-1">Explore our course catalog to find your next learning journey.</p>
                            <div class="mt-4">
                                <a href="{{ route('student.catalog.index') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm inline-flex items-center gap-1.5">
                                    Browse Courses &rarr;
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Attendance & Academic Progress Grid (Requirement #2) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Attendance Logs -->
                <div class="bg-white shadow-sm rounded-2xl border border-slate-200/80 overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base">Recent Attendance Logs</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Session tracking for your classes</p>
                        </div>
                        <a href="{{ route('student.attendance.index') }}" class="text-xs font-semibold text-emerald-600 hover:underline">
                            Full Log &rarr;
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($recentAttendances as $att)
                            <div class="p-4 hover:bg-slate-50/50 transition-colors flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-sm text-slate-900">{{ $att->class->course->title ?? $att->class->name }}</p>
                                    <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $att->date?->format('l, M d, Y') }}</p>
                                </div>
                                <span class="px-2.5 py-1 text-xs font-extrabold rounded-full 
                                    @if($att->status === 'Present') bg-emerald-100 text-emerald-800
                                    @elseif($att->status === 'Late') bg-amber-100 text-amber-800
                                    @else bg-rose-100 text-rose-800 @endif">
                                    {{ $att->status }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-8 text-center">No attendance sessions logged yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Grades & Evaluations -->
                <div class="bg-white shadow-sm rounded-2xl border border-slate-200/80 overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base">Academic Evaluations & Scores</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Exam scores and constructive feedback</p>
                        </div>
                        <a href="{{ route('student.grades.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                            Full Gradebook &rarr;
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($recentGrades as $grd)
                            <div class="p-4 hover:bg-slate-50/50 transition-colors flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700">
                                            {{ $grd->assessment_type }}
                                        </span>
                                        <p class="font-bold text-sm text-slate-900">{{ $grd->title }}</p>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $grd->class->course->title ?? 'Class' }} &bull; {{ $grd->assessment_date?->format('M d, Y') }}</p>
                                    @if($grd->feedback)
                                        <p class="text-xs text-slate-500 italic mt-1 bg-slate-50 p-1.5 rounded-lg border border-slate-100">
                                            "{{ $grd->feedback }}"
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl font-extrabold text-sm
                                        @if($grd->letter_grade === 'A') bg-emerald-100 text-emerald-800
                                        @elseif($grd->letter_grade === 'B') bg-blue-100 text-blue-800
                                        @elseif($grd->letter_grade === 'C') bg-amber-100 text-amber-800
                                        @elseif($grd->letter_grade === 'D') bg-orange-100 text-orange-800
                                        @else bg-rose-100 text-rose-800 @endif">
                                        {{ $grd->letter_grade }}
                                    </span>
                                    <p class="text-[11px] font-bold text-slate-600 mt-0.5">{{ $grd->score }} / {{ $grd->max_score }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-8 text-center">No exam or quiz evaluations recorded yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Invoices & Parent/Guardian Summary Grid (Requirements #3 & #5) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Invoices & Payments Summary (2 cols) -->
                <div class="lg:col-span-2 bg-white shadow-sm rounded-2xl border border-slate-200/80 overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base">Invoices & Payment History</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Billed tuition and payment receipts</p>
                        </div>
                        <a href="{{ route('student.invoices.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                            View Invoices &rarr;
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($recentPayments as $payment)
                            <div class="p-4 hover:bg-slate-50/50 transition-colors flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-sm text-slate-900">Receipt: {{ $payment->reference_number ?? '#' . $payment->id }}</p>
                                    <p class="text-xs text-slate-400 font-mono mt-0.5">
                                        Method: {{ $payment->payment_method }} &bull; {{ $payment->paid_at?->format('M d, Y') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="font-extrabold text-sm text-emerald-600">+${{ number_format($payment->amount_paid, 2) }}</span>
                                    <p class="text-xs text-slate-500">Invoice {{ $payment->invoice?->invoice_number }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-8 text-center">No payment receipts on file.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Linked Parent / Guardian Visibility (Requirement #5) -->
                <div class="bg-white shadow-sm rounded-2xl border border-slate-200/80 p-5 flex flex-col justify-between">
                    <div>
                        <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                            <h3 class="font-extrabold text-slate-900 text-base">👨‍👩‍👧 Parent / Guardian Links</h3>
                            <a href="{{ route('student.profile.edit') }}" class="text-xs text-slate-400 hover:text-slate-600">Info</a>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">
                            Associated guardians authorized to monitor your academic and financial progress.
                        </p>

                        <div class="mt-4 space-y-3">
                            @forelse($parents as $parent)
                                <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/70 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="font-extrabold text-slate-900">{{ $parent->name }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-200 text-amber-900">Guardian</span>
                                    </div>
                                    <div class="mt-1 text-slate-600 space-y-0.5 font-mono text-[11px]">
                                        <p>📞 {{ $parent->phone ?? 'No phone' }}</p>
                                        <p>✉️ {{ $parent->email }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="py-6 text-center text-xs text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                    No parent or guardian linked to this account.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="mt-6 pt-3 border-t border-slate-100">
                        <a href="{{ route('student.profile.edit') }}" class="w-full block text-center py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                            Manage Account & Security &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Course Discovery & Wishlist Banner (Requirement #4) -->
            <div class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 rounded-2xl p-6 sm:p-8 text-white shadow-md flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-indigo-700/60 text-indigo-200 border border-indigo-600/50">
                        Course Catalog &amp; Wishlist
                    </span>
                    <h3 class="text-xl font-extrabold mt-2 leading-tight">Ready to master a new skill?</h3>
                    <p class="text-xs text-indigo-200 mt-1 max-w-xl">
                        Browse all upcoming courses, syllabi, and schedules. You have <span class="font-bold text-white">{{ $wishlistedCount }} course(s)</span> saved in your wishlist for walk-in registration.
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('student.catalog.index', ['tab' => 'wishlist']) }}" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition-all">
                        My Wishlist ({{ $wishlistedCount }})
                    </a>
                    <a href="{{ route('student.catalog.index') }}" class="px-5 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-600 text-white font-extrabold text-xs shadow-md transition-all">
                        Explore Catalog &rarr;
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
