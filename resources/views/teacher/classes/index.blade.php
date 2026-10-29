<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-800 tracking-tight leading-tight">
                    {{ __('Assigned Classes & Schedules') }}
                </h2>
                <p class="text-sm text-slate-500 mt-1">Manage all your academic batches, room allocations, and schedules.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('teacher.attendance.create') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-all">
                    Mark Daily Attendance
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Messages -->
            <x-flash-messages />

            <!-- Filters & Search Bar -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <!-- Status Pills -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    <a href="{{ route('teacher.classes.index') }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors {{ empty($status) ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        All Batches
                    </a>
                    <a href="{{ route('teacher.classes.index', ['status' => 'active']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors {{ $status === 'active' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Active
                    </a>
                    <a href="{{ route('teacher.classes.index', ['status' => 'upcoming']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors {{ $status === 'upcoming' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Upcoming
                    </a>
                    <a href="{{ route('teacher.classes.index', ['status' => 'completed']) }}" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-colors {{ $status === 'completed' ? 'bg-purple-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Completed
                    </a>
                </div>

                <!-- Search Form -->
                <form method="GET" action="{{ route('teacher.classes.index') }}" class="flex items-center gap-2">
                    @if($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search class, room, course..."
                        class="px-3.5 py-1.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-emerald-200 focus:border-emerald-500 w-64">
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors">
                        Search
                    </button>
                    @if($search || $status)
                        <a href="{{ route('teacher.classes.index') }}" class="text-xs text-slate-500 hover:underline">Reset</a>
                    @endif
                </form>
            </div>

            <!-- Class Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($classes as $class)
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <span class="px-2.5 py-0.5 rounded-md text-xs font-bold {{ $class->status === 'active' ? 'bg-emerald-100 text-emerald-800' : ($class->status === 'upcoming' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ ucfirst($class->status) }}
                                </span>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded bg-blue-50 text-blue-700">
                                    📍 {{ $class->room ?? 'Room TBA' }}
                                </span>
                            </div>

                            <h3 class="text-lg font-extrabold text-slate-900 mt-3 leading-snug">
                                {{ $class->name }}
                            </h3>
                            <p class="text-xs font-bold text-indigo-600 mt-0.5">
                                {{ $class->course->title ?? 'Course Title' }}
                            </p>
                            <p class="text-xs text-slate-600 mt-2 line-clamp-3">
                                {{ $class->course->description ?? 'No curriculum overview.' }}
                            </p>

                            <!-- Class Meta -->
                            <div class="mt-5 pt-4 border-t border-slate-100 space-y-2 text-xs text-slate-600">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-slate-500">Days:</span>
                                    <span class="font-bold text-slate-800">{{ $class->days }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-slate-500">Time:</span>
                                    <span>{{ substr($class->start_time ?? '00:00', 0, 5) }} - {{ substr($class->end_time ?? '00:00', 0, 5) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-slate-500">Enrolled Students:</span>
                                    <span class="font-extrabold text-emerald-600">👥 {{ $class->students_count }} Students</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-slate-500">Recorded Sessions:</span>
                                    <span>📅 {{ $class->attendances_count }} entries</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action Hub -->
                        <div class="mt-6 pt-4 border-t border-slate-100 space-y-2">
                            <a href="{{ route('teacher.classes.show', $class->id) }}" class="w-full block text-center py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-colors">
                                Class Hub & Syllabus Outline
                            </a>
                            <div class="grid grid-cols-3 gap-1.5 text-center text-xs">
                                <a href="{{ route('teacher.attendance.create', ['class_id' => $class->id]) }}" class="py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold border border-emerald-200 transition-colors">
                                    Attendance
                                </a>
                                <a href="{{ route('teacher.roster.index', ['class_id' => $class->id]) }}" class="py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold border border-blue-200 transition-colors">
                                    Roster
                                </a>
                                <a href="{{ route('teacher.grades.index', ['class_id' => $class->id]) }}" class="py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold border border-indigo-200 transition-colors">
                                    Grades
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-2xl border border-dashed border-slate-300">
                        <p class="font-bold text-base">No classes found.</p>
                        <p class="text-xs text-slate-400 mt-1">Try clearing your search query or status filter.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div>
                {{ $classes->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
