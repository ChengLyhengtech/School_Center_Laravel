<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight">
                    {{ __('My Classes & Schedule') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Review your enrolled batches, study days, time slots, and classroom locations.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('student.catalog.index') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition-all inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Browse More Courses</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <x-flash-messages />

            <!-- Status Tabs -->
            <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
                <a href="{{ route('student.classes.index', ['status' => 'all']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'all' ? 'bg-slate-900 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    All Classes ({{ $enrolledClasses->count() }})
                </a>
                <a href="{{ route('student.classes.index', ['status' => 'active']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'active' ? 'bg-slate-900 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Active Batches
                </a>
                <a href="{{ route('student.classes.index', ['status' => 'completed']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $status === 'completed' ? 'bg-slate-900 text-white shadow-2xs' : 'text-slate-600 hover:bg-slate-100' }}">
                    Completed
                </a>
            </div>

            <!-- Enrolled Classes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($enrolledClasses as $class)
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <span class="px-2.5 py-0.5 rounded-md text-xs font-bold {{ $class->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($class->status) }}
                                </span>
                                <span class="text-xs font-bold px-2.5 py-0.5 rounded bg-blue-50 text-blue-700">
                                    📍 {{ $class->room ?? 'Room TBA' }}
                                </span>
                            </div>

                            <h3 class="text-lg font-extrabold text-slate-900 mt-3 leading-snug">
                                {{ $class->name }}
                            </h3>
                            <p class="text-xs font-semibold text-indigo-600 mt-0.5">
                                {{ $class->course->title }}
                            </p>

                            <!-- Class Timetable & Teacher Info -->
                            <div class="mt-5 p-4 rounded-xl bg-slate-50 border border-slate-100 space-y-2 text-xs">
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
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500 font-medium">Instructor Contact:</span>
                                    <span class="font-mono text-slate-700">{{ $class->teacher->phone ?? $class->teacher->email ?? 'N/A' }}</span>
                                </div>
                                <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
                                    <span class="text-slate-500 font-medium">Attendance Rate:</span>
                                    <span class="font-bold {{ ($class->attendance_rate ?? 0) >= 80 ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $class->attendance_rate !== null ? "{$class->attendance_rate}%" : 'No logs' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 pt-4 border-t border-slate-100">
                            <a href="{{ route('student.classes.show', $class) }}" class="w-full block text-center py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-2xs transition-colors">
                                View Syllabus, Materials & Outline &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-2xl border border-dashed border-slate-300">
                        <p class="font-bold text-slate-700">No enrolled classes found in this category.</p>
                        <p class="text-xs text-slate-400 mt-1">Explore available courses to register for upcoming batches.</p>
                        <div class="mt-4">
                            <a href="{{ route('student.catalog.index') }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm inline-flex items-center gap-1.5">
                                Browse Courses &rarr;
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
