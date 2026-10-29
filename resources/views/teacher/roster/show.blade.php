<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('teacher.roster.index', ['class_id' => $class->id]) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                        &larr; Back to Class Roster
                    </a>
                    <span class="text-slate-300">/</span>
                    <span class="text-xs font-bold text-slate-700">{{ $student->name }}</span>
                </div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight leading-tight">
                    {{ $student->name }} &mdash; <span class="text-indigo-600">{{ $class->name }}</span>
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('teacher.attendance.create', ['class_id' => $class->id]) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-all">
                    Mark Attendance
                </a>
                <a href="{{ route('teacher.grades.create', ['class_id' => $class->id]) }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition-all">
                    Enter Grade
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Student Profile Header Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-600 to-blue-500 text-white font-extrabold flex items-center justify-center text-xl shadow-md">
                        {{ strtoupper(substr($student->name, 0, 2)) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900">{{ $student->name }}</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            ✉️ {{ $student->email }} &bull; 📞 {{ $student->phone ?? 'No phone' }}
                        </p>
                        <p class="text-xs font-semibold text-indigo-600 mt-1">
                            Enrolled Course: {{ $class->course->title }} ({{ $class->name }})
                        </p>
                    </div>
                </div>

                <!-- Emergency Parent/Guardian Box -->
                <div class="p-4 rounded-xl bg-amber-50/80 border border-amber-200 text-xs md:max-w-xs w-full">
                    <p class="font-bold text-amber-900 flex items-center gap-1.5 mb-1">
                        <span>👨‍👩‍👦 Emergency / Parent Contact</span>
                    </p>
                    @if($student->parents->isNotEmpty())
                        @php $parent = $student->parents->first(); @endphp
                        <p class="font-extrabold text-slate-900">{{ $parent->name }}</p>
                        <p class="text-amber-900 font-mono mt-0.5">📞 {{ $parent->phone ?? 'No phone' }}</p>
                        <p class="text-amber-800 text-[11px]">✉️ {{ $parent->email }}</p>
                    @else
                        <p class="text-slate-500 italic">No parent / guardian linked to this student profile.</p>
                    @endif
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Attendance Rate</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">
                        {{ $attendanceRate !== null ? "{$attendanceRate}%" : 'N/A' }}
                    </p>
                    <span class="text-xs font-medium text-emerald-600">{{ $presentCount }} / {{ $totalSessions }} Sessions Present</span>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Late Arrivals</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-1">{{ $lateCount }}</p>
                    <span class="text-xs font-medium text-slate-500">Days Marked Late</span>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Unexcused Absences</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-rose-600 mt-1">{{ $absentCount }}</p>
                    <span class="text-xs font-medium {{ $absentCount >= 3 ? 'text-rose-600 font-bold' : 'text-slate-500' }}">
                        {{ $absentCount >= 3 ? '⚠️ Truancy Warning' : 'Within normal limits' }}
                    </span>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Academic Average</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-indigo-600 mt-1">
                        {{ $averageScore !== null ? "{$averageScore}%" : 'No grades' }}
                    </p>
                    <span class="text-xs font-medium text-slate-500">{{ $grades->count() }} Evaluated Tasks</span>
                </div>
            </div>

            <!-- Grades and Attendance Breakdown Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Academic Evaluation History -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-extrabold text-slate-900 text-base">Gradebook & Scores</h3>
                        <a href="{{ route('teacher.grades.create', ['class_id' => $class->id]) }}" class="text-xs font-bold text-indigo-600 hover:underline">
                            + Add Grade
                        </a>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($grades as $grade)
                            <div class="p-4 hover:bg-slate-50 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="font-extrabold text-sm text-slate-900">{{ $grade->title }}</p>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                            {{ $grade->assessment_type }}
                                        </span>
                                        <span class="text-xs text-slate-400 ms-2 font-mono">
                                            {{ $grade->assessment_date?->format('M d, Y') }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-base font-extrabold text-slate-900">{{ $grade->score }} / {{ $grade->max_score }}</span>
                                        <span class="ms-1.5 px-2 py-0.5 text-xs font-bold rounded-md bg-indigo-50 text-indigo-700">
                                            {{ $grade->letter_grade }} ({{ $grade->percentage }}%)
                                        </span>
                                    </div>
                                </div>
                                @if($grade->feedback)
                                    <div class="mt-2.5 p-2.5 rounded-lg bg-slate-50 text-xs text-slate-600 italic">
                                        &ldquo;{{ $grade->feedback }}&rdquo;
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-6 text-center">No scores recorded for this student yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Attendance History Log -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-extrabold text-slate-900 text-base">Attendance History Log</h3>
                        <span class="text-xs font-medium text-slate-500">{{ $attendances->count() }} Recorded Sessions</span>
                    </div>
                    <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                        @forelse($attendances as $att)
                            <div class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition-colors">
                                <span class="font-semibold text-xs text-slate-800 font-mono">
                                    {{ $att->date?->format('l, M d, Y') }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $att->status === 'Present' ? 'bg-emerald-100 text-emerald-800' : ($att->status === 'Late' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $att->status }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-6 text-center">No attendance sessions logged for this student.</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
