<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight">
                    {{ __('Official Student Rosters & Parent Contacts') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Communicate with students and access emergency parent/guardian contacts.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('teacher.attendance.create') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-all">
                    Mark Attendance
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Messages -->
            <x-flash-messages />

            <!-- Filters & Class Selector -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <form method="GET" action="{{ route('teacher.roster.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                    <!-- Class Selector -->
                    <div>
                        <select name="class_id" onchange="this.form.submit()" class="px-3.5 py-1.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 font-semibold text-slate-700">
                            <option value="">All My Classes & Rosters</option>
                            @foreach($myClasses as $c)
                                <option value="{{ $c->id }}" {{ (string)$classId === (string)$c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->course->title }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search student name, email, phone..."
                            class="px-3.5 py-1.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 w-64">
                    </div>

                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors">
                        Filter
                    </button>

                    @if($classId || $search)
                        <a href="{{ route('teacher.roster.index') }}" class="text-xs text-slate-500 hover:underline">Reset</a>
                    @endif
                </form>
            </div>

            <!-- Class Groups and Rosters -->
            @forelse($classesWithRosters as $classGroup)
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-extrabold text-slate-900 text-base">{{ $classGroup->name }}</h3>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-blue-100 text-blue-800">
                                    📍 {{ $classGroup->room ?? 'Room TBA' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Course: <span class="font-bold text-slate-700">{{ $classGroup->course->title }}</span> &bull; Schedule: {{ $classGroup->days }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-100">
                                👥 {{ $classGroup->students->count() }} Students Enrolled
                            </span>
                            <a href="{{ route('teacher.attendance.create', ['class_id' => $classGroup->id]) }}" class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-colors">
                                Take Attendance
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider text-[11px] font-bold border-b border-slate-100">
                                <tr>
                                    <th class="py-3 px-5">Student Information</th>
                                    <th class="py-3 px-5">Contact Details</th>
                                    <th class="py-3 px-5">Enrolled Date</th>
                                    <th class="py-3 px-5">Linked Parent / Guardian Contacts (Emergency)</th>
                                    <th class="py-3 px-5 text-right">Academic Profile</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($classGroup->students as $student)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <!-- Student Info -->
                                        <td class="py-3.5 px-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-500 to-blue-500 text-white font-extrabold flex items-center justify-center text-xs shadow-2xs">
                                                    {{ strtoupper(substr($student->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="font-extrabold text-slate-900 text-sm">{{ $student->name }}</div>
                                                    <div class="text-[11px] text-slate-400">Student ID: #{{ $student->id }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Contact Details -->
                                        <td class="py-3.5 px-5">
                                            <p class="font-medium text-slate-800">{{ $student->email }}</p>
                                            <p class="text-[11px] text-slate-500">{{ $student->phone ?? 'No phone recorded' }}</p>
                                        </td>

                                        <!-- Enrolled Date -->
                                        <td class="py-3.5 px-5 font-mono">
                                            {{ $student->pivot->enrolled_date ?? 'Active' }}
                                        </td>

                                        <!-- Parent Link Visibility (Requirement #2) -->
                                        <td class="py-3.5 px-5">
                                            @if($student->parents->isNotEmpty())
                                                @foreach($student->parents as $parent)
                                                    <div class="p-2 rounded-xl bg-amber-50/70 border border-amber-200/60 inline-block mb-1">
                                                        <div class="font-bold text-amber-950 text-xs flex items-center gap-1">
                                                            <span>👨‍👩‍👦 {{ $parent->name }}</span>
                                                            <span class="text-[10px] bg-amber-200 text-amber-900 px-1.5 py-0.2 rounded font-semibold">Guardian</span>
                                                        </div>
                                                        <div class="text-[11px] text-amber-800 font-mono mt-0.5">
                                                            📞 {{ $parent->phone ?? 'No phone' }} &bull; ✉️ {{ $parent->email }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-slate-400 italic text-[11px]">Self-enrolled adult learner</span>
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                        <td class="py-3.5 px-5 text-right">
                                            <a href="{{ route('teacher.roster.show', ['class' => $classGroup->id, 'student' => $student->id]) }}" class="px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 font-bold hover:bg-blue-100 transition-colors inline-flex items-center gap-1">
                                                <span>Performance Card</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-slate-400">
                                            No students found in this class section.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="py-16 text-center text-slate-500 bg-white rounded-2xl border border-dashed border-slate-300">
                    <p class="font-bold text-base">No rosters available.</p>
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>
