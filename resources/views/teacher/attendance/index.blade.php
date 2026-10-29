<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight">
                    {{ __('Attendance Management Hub') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Record daily attendance, monitor consistency, and track truancy trends.</p>
            </div>
            <div>
                <a href="{{ route('teacher.attendance.create') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Mark Daily Attendance</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Messages -->
            <x-flash-messages />

            <!-- Classes Attendance Summary Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($classes as $class)
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <span class="px-2.5 py-0.5 rounded-md text-xs font-bold {{ $class->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($class->status) }}
                                </span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded bg-blue-50 text-blue-700">
                                    📍 {{ $class->room ?? 'Room TBA' }}
                                </span>
                            </div>

                            <h3 class="text-lg font-extrabold text-slate-900 mt-3 leading-snug">
                                {{ $class->name }}
                            </h3>
                            <p class="text-xs font-semibold text-indigo-600 mt-0.5">
                                {{ $class->course->title }}
                            </p>

                            <!-- Metric Snapshot -->
                            <div class="mt-5 p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-2 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Overall Attendance Rate:</span>
                                    <span class="font-extrabold {{ ($class->attendance_rate ?? 0) >= 80 ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $class->attendance_rate !== null ? "{$class->attendance_rate}%" : 'No logs yet' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Enrolled Students:</span>
                                    <span class="font-bold text-slate-800">{{ $class->students_count }} Students</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Last Recorded:</span>
                                    <span class="font-mono text-slate-700">{{ $class->last_attendance_date ?? 'Not yet marked' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 pt-4 border-t border-slate-100 grid grid-cols-2 gap-2">
                            <a href="{{ route('teacher.attendance.create', ['class_id' => $class->id]) }}" class="text-center py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-2xs transition-colors">
                                Take Today's
                            </a>
                            <a href="{{ route('teacher.attendance.history', ['class_id' => $class->id]) }}" class="text-center py-2.5 px-3 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-300 transition-colors">
                                History & Truancy
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-2xl border border-dashed border-slate-300">
                        No assigned classes available for attendance.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
