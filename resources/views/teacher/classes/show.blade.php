<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('teacher.classes.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                        &larr; Back to Classes
                    </a>
                    <span class="text-slate-300">/</span>
                    <span class="text-xs font-bold text-slate-700">{{ $class->name }}</span>
                </div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight leading-tight">
                    {{ $class->name }} &mdash; <span class="text-indigo-600">{{ $class->course->title }}</span>
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('teacher.attendance.create', ['class_id' => $class->id]) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-all">
                    Mark Attendance
                </a>
                <a href="{{ route('teacher.grades.create', ['class_id' => $class->id]) }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition-all">
                    Record Grades
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Messages -->
            <x-flash-messages />

            <!-- Overview Meta Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Schedule & Room</span>
                    <p class="text-base font-extrabold text-slate-900 mt-1">📍 {{ $class->room ?? 'Room TBA' }}</p>
                    <p class="text-xs text-slate-600 mt-0.5">{{ $class->days }} ({{ substr($class->start_time ?? '00:00', 0, 5) }} - {{ substr($class->end_time ?? '00:00', 0, 5) }})</p>
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
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Official Roster</span>
                    <p class="text-2xl font-extrabold text-emerald-600 mt-1">👥 {{ $enrolledCount }}</p>
                    <a href="{{ route('teacher.roster.index', ['class_id' => $class->id]) }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                        View Full Roster &rarr;
                    </a>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Attendance Rate</span>
                    <p class="text-2xl font-extrabold text-slate-900 mt-1">
                        {{ $attendanceRate !== null ? "{$attendanceRate}%" : 'No logs' }}
                    </p>
                    <a href="{{ route('teacher.attendance.history', ['class_id' => $class->id]) }}" class="text-xs font-semibold text-emerald-600 hover:underline">
                        History & Truancy &rarr;
                    </a>
                </div>
            </div>

            <!-- Syllabus & Curriculum Outline -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-extrabold text-slate-900 text-base">Course Syllabus & Curriculum Outline</h3>
                    <span class="text-xs font-semibold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-lg">
                        Standard Fee: ${{ number_format($class->course->price, 2) }}
                    </span>
                </div>
                <div class="prose max-w-none text-sm text-slate-600 leading-relaxed">
                    <p>{{ $class->course->description ?? 'No detailed syllabus text provided by administration.' }}</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/60 flex flex-wrap items-center gap-6 text-xs text-slate-600">
                    <div><span class="font-bold text-slate-800">Primary Instructor:</span> {{ $class->teacher->name }}</div>
                    <div><span class="font-bold text-slate-800">Instructor Contact:</span> {{ $class->teacher->email }} / {{ $class->teacher->phone ?? 'No phone' }}</div>
                    <div><span class="font-bold text-slate-800">Qualifications:</span> {{ $class->teacher->qualifications ?? 'Faculty Certified' }}</div>
                </div>
            </div>

            <!-- Student Roster & Emergency Contacts Preview -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Enrolled Student Roster</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Includes linked parent/guardian contacts for student performance discussions and emergencies</p>
                    </div>
                    <a href="{{ route('teacher.roster.index', ['class_id' => $class->id]) }}" class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors">
                        View Detailed Roster
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-100">
                            <tr>
                                <th class="py-3 px-5">Student Name</th>
                                <th class="py-3 px-5">Contact Details</th>
                                <th class="py-3 px-5">Enrolled Date</th>
                                <th class="py-3 px-5">Parent / Guardian Link</th>
                                <th class="py-3 px-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($class->students as $student)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="py-3.5 px-5">
                                        <div class="font-bold text-slate-900 text-sm">{{ $student->name }}</div>
                                        <div class="text-[11px] text-slate-400">ID: #{{ $student->id }}</div>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        <p class="font-medium text-slate-800">{{ $student->email }}</p>
                                        <p class="text-[11px] text-slate-500">{{ $student->phone ?? 'No phone' }}</p>
                                    </td>
                                    <td class="py-3.5 px-5">
                                        {{ $student->pivot->enrolled_date ?? 'Active' }}
                                    </td>
                                    <td class="py-3.5 px-5">
                                        @if($student->parents->isNotEmpty())
                                            @php $parent = $student->parents->first(); @endphp
                                            <span class="inline-flex items-center gap-1 font-semibold text-slate-800">
                                                👨‍👩‍👦 {{ $parent->name }}
                                            </span>
                                            <div class="text-[11px] text-slate-500 font-mono">
                                                📞 {{ $parent->phone ?? $parent->email }}
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic">Self-Enrolled / Adult</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-5 text-right">
                                        <a href="{{ route('teacher.roster.show', ['class' => $class->id, 'student' => $student->id]) }}" class="px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-700 font-bold hover:bg-blue-100 transition-colors">
                                            Performance Card &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">
                                        No students enrolled in this class batch yet.
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
