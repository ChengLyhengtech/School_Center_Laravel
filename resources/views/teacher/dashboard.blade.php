<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight">
                    {{ __('Teacher Portal Dashboard') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Welcome back, {{ $teacher->name }}! Manage your schedules, student rosters, daily attendance, and score evaluations.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Faculty Instructor
                </span>
                <a href="{{ route('teacher.attendance.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm shadow-emerald-600/30 transition-all hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span>Mark Attendance</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Status Messages -->
            <x-flash-messages />

            <!-- Key Metric Statistics Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                <!-- Stat 1 -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Assigned Classes</span>
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $classes->count() }}</p>
                        <span class="text-xs font-medium text-emerald-600">{{ $activeClassesCount }} Active Batches</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                </div>

                <!-- Stat 2 -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Enrolled</span>
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $totalStudentsCount }}</p>
                        <span class="text-xs font-medium text-slate-500">Official Roster Students</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>

                <!-- Stat 3 -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Today's Attendance</span>
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">
                            {{ $todayAttendanceRate !== null ? "{$todayAttendanceRate}%" : 'Pending' }}
                        </p>
                        <span class="text-xs font-medium text-slate-500">{{ $todayAttendances->count() }} submissions logged</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <!-- Stat 4 -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Recent Evaluations</span>
                        <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">{{ $recentGrades->count() }}</p>
                        <span class="text-xs font-medium text-indigo-600">Gradebook Entries</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Schedule & Assigned Classes Section -->
            <div class="bg-white shadow-sm rounded-2xl border border-slate-200/80 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-slate-800 text-base">Class Schedule & Course Hub</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Your official teaching batches, classrooms, and schedules</p>
                    </div>
                    <a href="{{ route('teacher.classes.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">
                        View All Classes &rarr;
                    </a>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($classes as $class)
                        <div class="border border-slate-200/80 rounded-2xl p-5 hover:border-emerald-400 hover:shadow-md transition-all duration-200 bg-gradient-to-b from-white to-slate-50/50 flex flex-col justify-between">
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-md {{ $class->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ ucfirst($class->status) }}
                                    </span>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded bg-blue-50 text-blue-700">
                                        📍 {{ $class->room ?? 'Room TBA' }}
                                    </span>
                                </div>

                                <h4 class="font-extrabold text-slate-900 text-base mt-3 leading-snug">
                                    {{ $class->name }}
                                </h4>
                                <p class="text-xs font-semibold text-indigo-600 mt-0.5">
                                    {{ $class->course->title ?? 'Course Title' }}
                                </p>
                                <p class="text-xs text-slate-600 mt-2 line-clamp-2">
                                    {{ $class->course->description ?? 'No curriculum outline available.' }}
                                </p>

                                <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-600 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-slate-500">Schedule:</span>
                                        <span class="font-bold text-slate-700">{{ $class->days }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-slate-500">Time:</span>
                                        <span>{{ substr($class->start_time ?? '00:00', 0, 5) }} - {{ substr($class->end_time ?? '00:00', 0, 5) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium text-slate-500">Students:</span>
                                        <span class="font-semibold text-emerald-600">👥 {{ $class->students_count }} Enrolled</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2">
                                <a href="{{ route('teacher.attendance.create', ['class_id' => $class->id]) }}" class="text-center px-3 py-2 text-xs font-bold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-2xs transition-colors">
                                    Attendance
                                </a>
                                <a href="{{ route('teacher.classes.show', $class->id) }}" class="text-center px-3 py-2 text-xs font-bold rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 transition-colors">
                                    Class Hub
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center text-slate-500">
                            No classes currently assigned to your account.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Attendance & Score Evaluation Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Attendance Logs -->
                <div class="bg-white shadow-sm rounded-2xl border border-slate-200/80 overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-extrabold text-slate-800 text-base">Recent Attendance Submissions</h3>
                        <a href="{{ route('teacher.attendance.history') }}" class="text-xs font-semibold text-emerald-600 hover:underline">
                            Full History &rarr;
                        </a>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($recentAttendances as $attendance)
                            <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                                <div>
                                    <p class="font-bold text-sm text-slate-900">{{ $attendance->student->name ?? 'Student' }}</p>
                                    <p class="text-xs text-slate-500">{{ $attendance->class->name ?? 'Class' }} &bull; {{ $attendance->date?->format('M d, Y') }}</p>
                                </div>
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $attendance->status === 'Present' ? 'bg-emerald-100 text-emerald-800' : ($attendance->status === 'Late' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $attendance->status }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-6 text-center">No attendance logged yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Grades Evaluated -->
                <div class="bg-white shadow-sm rounded-2xl border border-slate-200/80 overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-extrabold text-slate-800 text-base">Recent Assessments & Evaluations</h3>
                        <a href="{{ route('teacher.grades.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                            Gradebook &rarr;
                        </a>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($recentGrades as $grade)
                            <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                                <div>
                                    <p class="font-bold text-sm text-slate-900">{{ $grade->student->name ?? 'Student' }}</p>
                                    <p class="text-xs text-slate-500">{{ $grade->title }} ({{ $grade->assessment_type }}) &bull; {{ $grade->class->name ?? 'Class' }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="font-extrabold text-sm text-slate-900">{{ $grade->score }} / {{ $grade->max_score }}</span>
                                    <span class="ms-1.5 px-2 py-0.5 text-xs font-bold rounded-md bg-indigo-50 text-indigo-700">
                                        {{ $grade->letter_grade }} ({{ $grade->percentage }}%)
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-6 text-center">No assessments evaluated yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
