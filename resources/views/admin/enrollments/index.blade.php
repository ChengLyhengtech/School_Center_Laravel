<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Enrollment & Student Placement') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Place students into active class schedules and manage school-wide rosters.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <!-- Student Placement Quick Action Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 space-y-3">
                <h3 class="text-sm font-bold uppercase text-gray-700 tracking-wider">Direct Student Placement</h3>
                <form action="{{ route('admin.enrollments.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Select Student <span class="text-red-500">*</span></label>
                        <select name="student_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Choose Student --</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Target Class Batch <span class="text-red-500">*</span></label>
                        <select name="class_id" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Choose Class --</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ $classFilter == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->course->title ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 mb-1">Enrollment Date <span class="text-red-500">*</span></label>
                        <input type="date" name="enrolled_date" value="{{ date('Y-m-d') }}" required class="w-full text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                            Place Student
                        </button>
                    </div>
                </form>
            </div>

            <!-- Filter & Search Bar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col md:flex-row items-center justify-between gap-3">
                <form method="GET" action="{{ route('admin.enrollments.index') }}" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <select name="class_id" class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- All Classes --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classFilter == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>

                    <input type="text" name="search" value="{{ $search }}" placeholder="Search student name..." class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 w-48">
                    
                    <button type="submit" class="px-3 py-1.5 bg-gray-800 text-white text-xs rounded-lg hover:bg-gray-900 transition">Filter</button>
                    @if($classFilter || $search)
                        <a href="{{ route('admin.enrollments.index') }}" class="text-xs text-gray-500 hover:text-gray-800 underline">Reset</a>
                    @endif
                </form>

                <span class="text-xs text-gray-500">{{ $enrollments->total() }} Total Placements</span>
            </div>

            <!-- Enrollments Table -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Student</th>
                                <th class="px-6 py-4">Course</th>
                                <th class="px-6 py-4">Class Batch & Schedule</th>
                                <th class="px-6 py-4">Teacher</th>
                                <th class="px-6 py-4">Enrolled Date</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($enrollments as $enr)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-6 py-4 flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($enr->student->name ?? 'S', 0, 2)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.users.show', $enr->student_id) }}" class="font-semibold text-gray-900 hover:text-indigo-600">
                                                {{ $enr->student->name ?? 'Unknown Student' }}
                                            </a>
                                            <p class="text-xs text-gray-400">{{ $enr->student->email ?? '' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-gray-800">
                                        {{ $enr->class->course->title ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-600">
                                        <a href="{{ route('admin.classes.show', $enr->class_id) }}" class="font-bold text-gray-900 hover:text-indigo-600">
                                            {{ $enr->class->name ?? '-' }}
                                        </a>
                                        <div class="text-[11px] text-gray-400">{{ $enr->class->room ?? 'Room N/A' }} &bull; {{ $enr->class->days ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-700">
                                        {{ $enr->class->teacher->name ?? 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs font-mono text-gray-500">
                                        {{ $enr->enrolled_date?->format('M d, Y') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('admin.enrollments.destroy', $enr) }}" method="POST" class="inline" onsubmit="return confirm('Remove student from this class?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-semibold">
                                                Unenroll
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-400 text-xs">
                                        No enrollment records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($enrollments->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $enrollments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
