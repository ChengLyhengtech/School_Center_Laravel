<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Class & Batch Scheduling') }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">Manage batch schedules, assigned teachers, classrooms, and statuses.</p>
            </div>
            <a href="{{ route('admin.classes.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Schedule New Class
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-flash-messages />

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <form method="GET" action="{{ route('admin.classes.index') }}" class="flex flex-wrap items-center gap-3">
                    <select name="status" class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- All Statuses --</option>
                        <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>

                    <select name="course_id" class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 max-w-xs">
                        <option value="">-- All Courses --</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ $courseFilter == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                        @endforeach
                    </select>

                    <input type="text" name="search" value="{{ $search }}" placeholder="Search class name or room..." class="text-xs rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 w-52">
                    
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-xs rounded-lg hover:bg-gray-900 transition">Filter</button>
                    @if($statusFilter || $courseFilter || $search)
                        <a href="{{ route('admin.classes.index') }}" class="text-xs text-gray-500 hover:text-gray-800 underline">Clear</a>
                    @endif
                </form>
            </div>

            <!-- Classes Table -->
            <div class="bg-white shadow-sm rounded-xl border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Class Batch</th>
                                <th class="px-6 py-4">Course</th>
                                <th class="px-6 py-4">Teacher</th>
                                <th class="px-6 py-4">Room & Schedule</th>
                                <th class="px-6 py-4">Enrolled</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($classes as $cls)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.classes.show', $cls) }}" class="font-bold text-gray-900 hover:text-indigo-600">
                                            {{ $cls->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-gray-800">
                                        {{ $cls->course->title ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <span class="font-semibold text-gray-700">{{ $cls->teacher->name ?? 'Unassigned' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-500">
                                        <div>Room: {{ $cls->room ?? 'N/A' }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $cls->days ?? 'Schedule TBA' }} ({{ $cls->start_time ?? '-' }} - {{ $cls->end_time ?? '-' }})</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                            {{ $cls->students_count }} students
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                            {{ $cls->status === 'active' ? 'bg-emerald-100 text-emerald-800' : ($cls->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($cls->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('admin.classes.show', $cls) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Roster</a>
                                        <a href="{{ route('admin.classes.edit', $cls) }}" class="text-xs font-semibold text-gray-600 hover:text-gray-900">Edit</a>
                                        <form action="{{ route('admin.classes.destroy', $cls) }}" method="POST" class="inline" onsubmit="return confirm('Delete this class?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-700">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-400 text-xs">
                                        No classes found. Click "Schedule New Class" to create one.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($classes->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $classes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
