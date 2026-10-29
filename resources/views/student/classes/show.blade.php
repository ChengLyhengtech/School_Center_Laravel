<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('student.classes.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                        &larr; Back to Enrolled Classes
                    </a>
                    <span class="text-slate-300">/</span>
                    <span class="text-xs font-bold text-slate-700">{{ $class->name }}</span>
                </div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight leading-tight">
                    {{ $class->name }} &mdash; <span class="text-indigo-600">{{ $class->course->title }}</span>
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('student.attendance.index', ['class_id' => $class->id]) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-all">
                    Attendance Log
                </a>
                <a href="{{ route('student.grades.index', ['class_id' => $class->id]) }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition-all">
                    Class Grades
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-flash-messages />

            <!-- Overview Meta Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Classroom & Room</span>
                    <p class="text-base font-extrabold text-slate-900 mt-1">📍 {{ $class->room ?? 'TBA' }}</p>
                    <p class="text-xs text-slate-600 mt-0.5">{{ $class->schedule_day }} ({{ $class->schedule_time }})</p>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Batch Status</span>
                    <div class="mt-1">
                        <span class="px-2.5 py-1 text-xs font-extrabold rounded-md {{ $class->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' }}">
                            {{ ucfirst($class->status) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Duration: {{ $class->course->start_date?->format('M d') }} - {{ $class->course->end_date?->format('M d, Y') }}</p>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Attendance Rate</span>
                    <p class="text-2xl font-extrabold {{ ($attendanceRate ?? 0) >= 80 ? 'text-emerald-600' : 'text-amber-600' }} mt-1">
                        {{ $attendanceRate !== null ? "{$attendanceRate}%" : 'No logs' }}
                    </p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $presentSessions }} / {{ $totalSessions }} Sessions present</p>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Grade Average</span>
                    <p class="text-2xl font-extrabold text-indigo-600 mt-1">
                        {{ $gradeAverage !== null ? "{$gradeAverage}%" : 'No grades' }}
                    </p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $grades->count() }} evaluations recorded</p>
                </div>
            </div>

            <!-- Course Description, Syllabus & Materials (Requirement #1) -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-extrabold text-slate-900 text-base">Course Description & Syllabus Outline</h3>
                    <span class="text-xs font-semibold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-lg">
                        Standard Fee: ${{ number_format($class->course->price, 2) }}
                    </span>
                </div>

                <div class="prose max-w-none text-sm text-slate-600 leading-relaxed">
                    <p>{{ $class->course->description }}</p>
                </div>

                <!-- Instructor Info Box -->
                <div class="bg-slate-50 p-5 rounded-xl border border-slate-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs text-slate-600">
                    <div>
                        <div class="font-extrabold text-sm text-slate-900">👨‍🏫 Assigned Instructor: {{ $class->teacher->name ?? 'Faculty' }}</div>
                        <p class="text-slate-500 mt-0.5">Qualifications: {{ $class->teacher->qualifications ?? 'Academic Staff' }}</p>
                        @if($class->teacher->bio)
                            <p class="text-slate-600 mt-2 italic max-w-xl">"{{ $class->teacher->bio }}"</p>
                        @endif
                    </div>
                    <div class="sm:text-right shrink-0 space-y-1">
                        <div class="font-bold text-slate-800">Direct Contact:</div>
                        <div class="font-mono text-slate-700">📞 {{ $class->teacher->phone ?? 'Contact center desk' }}</div>
                        <div class="font-mono text-slate-700">✉️ {{ $class->teacher->email }}</div>
                    </div>
                </div>
            </div>

            <!-- Class Attendance & Grade Submissions Breakdown -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Personal Attendance Logs -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-extrabold text-slate-900 text-base">My Class Attendance Logs</h3>
                        <span class="text-xs font-bold text-slate-500">{{ $totalSessions }} Sessions</span>
                    </div>

                    <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                        @forelse($attendances as $att)
                            <div class="p-3.5 px-5 flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-bold text-slate-800 font-mono">{{ $att->date?->format('l, M d, Y') }}</span>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full font-bold
                                    @if($att->status === 'Present') bg-emerald-100 text-emerald-800
                                    @elseif($att->status === 'Late') bg-amber-100 text-amber-800
                                    @else bg-rose-100 text-rose-800 @endif">
                                    {{ $att->status }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-6 text-center">No attendance recorded in this class.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Academic Evaluations & Feedback -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-extrabold text-slate-900 text-base">Grades & Instructor Feedback</h3>
                        <span class="text-xs font-bold text-slate-500">{{ $grades->count() }} Tasks Graded</span>
                    </div>

                    <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                        @forelse($grades as $grd)
                            <div class="p-4 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700">
                                            {{ $grd->assessment_type }}
                                        </span>
                                        <span class="font-bold text-sm text-slate-800">{{ $grd->title }}</span>
                                    </div>
                                    <span class="font-mono font-bold text-xs px-2 py-0.5 rounded bg-slate-100 text-slate-800">
                                        {{ $grd->score }} / {{ $grd->max_score }} ({{ $grd->percentage }}%)
                                    </span>
                                </div>
                                @if($grd->feedback)
                                    <p class="text-xs text-slate-600 bg-slate-50 p-2 rounded-lg border border-slate-100 italic">
                                        Feedback: "{{ $grd->feedback }}"
                                    </p>
                                @endif
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-6 text-center">No scores or evaluations recorded yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
